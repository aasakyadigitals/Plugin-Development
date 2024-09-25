const express = require('express');
const bodyParser = require('body-parser');
const admin = require('firebase-admin');
const serviceAccount = require('./firebase-service-account.json'); // Your Firebase service account file

// Initialize Firebase Admin SDK
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
});

const app = express();
app.use(bodyParser.json());

// Function to send notification
const sendNotification = (registrationToken, title, body) => {
  const message = {
    notification: {
      title: title,
      body: body,
    },
    token: registrationToken,
  };

  admin
    .messaging()
    .send(message)
    .then((response) => {
      console.log('Successfully sent message:', response);
    })
    .catch((error) => {
      console.log('Error sending message:', error);
    });
};

// API endpoint to trigger notifications
app.post('/send-notification', (req, res) => {
  const { registrationToken, title, body } = req.body;

  if (!registrationToken || !title || !body) {
    return res.status(400).send('Invalid request');
  }

  sendNotification(registrationToken, title, body);
  res.status(200).send('Notification sent');
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
