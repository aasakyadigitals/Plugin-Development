const { firebase } = require("../firebase/firebase");

const sendNotificationFirebase = async (token, title, body) => {
    console.log("SendNotification : ", token, title, body)
  try {
    await firebase.messaging().send({
      token: token,
      notification: {
        title: title,
        body: body,
      },
    });
    console.log("Notification Send Successfully....");
  } catch (error) {
    console.log("notification failed");
  }
};

module.exports = { sendNotificationFirebase };
