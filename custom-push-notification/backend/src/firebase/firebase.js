
const firebase = require("firebase-admin");

const serviceAccount = require("./custom-push-notification-2c2d7-firebase-adminsdk-j4qvn-b85fdaf84c.json");

firebase.initializeApp({
  credential: firebase.credential.cert(serviceAccount)
});

module.exports = {firebase}
