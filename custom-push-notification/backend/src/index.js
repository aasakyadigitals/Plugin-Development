const express = require("express");
const bodyParser = require("body-parser");
const { notificationRoutes } = require("./routes/notification-routes");
const cors = require("cors");

const PORT = process.env.PORT || 3000;
const app = express();

app.use(cors());
app.use(bodyParser.json({ limit: "100mb" }));
app.use(bodyParser.urlencoded({ extended: true, limit: "100mb" }));

app.get("/", (req, res) => {
  res.send("Hello World");
});

app.use("/api", notificationRoutes);

app.listen(PORT, () => {
  console.log(`App is listening on PORT No. ${PORT}`);
});
