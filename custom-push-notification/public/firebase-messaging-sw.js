// firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js');

// Initialize Firebase
firebase.initializeApp({
    apiKey: "AIzaSyBvbWQU7MVYJybgBgeDVIIJsWQ8TG88Nfw",
    authDomain: "custom-push-notification-2c2d7.firebaseapp.com",
    projectId: "custom-push-notification-2c2d7",
    storageBucket: "custom-push-notification-2c2d7.appspot.com",
    messagingSenderId: "172660199967",
    appId: "1:172660199967:web:178b9c8a8c5a43088bfb2a",
    measurementId: "G-XW2SPSX1LQ",
  // Optionally, include other Firebase config parameters
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/firebase-logo.png' // Optional: Add your own icon
  };

  self.registration.showNotification(notificationTitle, notificationOptions)
    .then(() => {
      console.log('[firebase-messaging-sw.js] Notification displayed:', notificationTitle);
    })
    .catch((err) => {
      console.error('[firebase-messaging-sw.js] Error displaying notification:', err);
    });
});

// Handle notification click events
self.addEventListener('notificationclick', function(event) {
    console.log('[firebase-messaging-sw.js] Notification click Received.', event.notification);
    event.notification.close();
    event.waitUntil(
        clients.openWindow('https://your-wordpress-site.com') // Replace with your desired URL
    );
    console.log('[firebase-messaging-sw.js] Notification click handled.');
});
