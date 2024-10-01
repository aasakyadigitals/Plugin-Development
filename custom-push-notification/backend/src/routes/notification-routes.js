const express = require("express");
const { sendNotification } = require("../controllers/notification-controllers");

const notificationRoutes = express.Router();

notificationRoutes.post("/send-notification", sendNotification);

module.exports = { notificationRoutes };
