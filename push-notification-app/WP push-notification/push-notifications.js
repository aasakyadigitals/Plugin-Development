document.addEventListener('DOMContentLoaded', function() {
    // Check if service worker is supported
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            }).catch(function(err) {
                console.log('Service Worker registration failed:', err);
            });
    }

    // Example of triggering a notification
    function sendPushNotification(title, body) {
        fetch(NOTIFICATION_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                registrationToken: 'USER_DEVICE_TOKEN', // Replace with the actual device token
                title: title,
                body: body,
            }),
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    // Example usage: sendPushNotification('New Comment', 'You have a new comment on your post.');
});
