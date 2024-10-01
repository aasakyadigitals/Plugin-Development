const { sendNotificationFirebase } = require("../utils/utils");

const sendNotification = async (req, res) => {
  try {
    const { fcmToken, title, body } = req.body;

    console.log(req.body)

    await sendNotificationFirebase(fcmToken, title, body);

    return res
      .status(200)
      .json({ success: true, message: "Message sent successfully" });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

module.exports = { sendNotification };
