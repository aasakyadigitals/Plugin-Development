// firebase-init.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Firebase
    firebase.initializeApp(wpFirebasePush.firebaseConfig);
    console.log('Firebase initialized with config:', wpFirebasePush.firebaseConfig);

    const messaging = firebase.messaging();

    // Request permission to send notifications
    function requestPermission() {
        console.log('Requesting notification permission...');
        return Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                return getToken();
            } else {
                console.log('Unable to get permission to notify.');
            }
        });
    }

    // Get FCM token
    function getToken() {
        // Use the VAPID key passed from PHP
        return messaging.getToken({ vapidKey: wpFirebasePush.vapidKey }).then((currentToken) => {
            if (currentToken) {
                console.log('FCM Token:', currentToken);
                // Send the token to your server
                sendTokenToServer(currentToken);
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });
    }

    // Send FCM token to the server via AJAX
    function sendTokenToServer(token) {
        console.log('Sending FCM token to server...');
        fetch(wpFirebasePush.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'save_fcm_token',
                fcmToken: token,
                nonce: wpFirebasePush.nonce,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('FCM Token saved successfully.');
            } else {
                console.error('Error saving FCM Token:', data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    // Handle incoming messages when the web app is in the foreground
    messaging.onMessage((payload) => {
        console.log('Message received. ', payload);
        // Customize notification here
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon || '/firebase-logo.png',
        };

        if (Notification.permission === 'granted') {
            new Notification(notificationTitle, notificationOptions);
            console.log('Displayed notification:', notificationTitle, notificationOptions);
        }
    });

    // Request permission on load
    requestPermission();
});
