// Firebase Service Worker for FCM
importScripts(
  "https://www.gstatic.com/firebasejs/9.10.0/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.10.0/firebase-messaging-compat.js"
);

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyCpgEcZuvQW70JHFbJ2mBHlM_hf8DsPWvQ",
  authDomain: "printing-commerce.firebaseapp.com",
  projectId: "printing-commerce",
  storageBucket: "printing-commerce.firebasestorage.app",
  messagingSenderId: "244660030535",
  appId: "1:244660030535:web:3340b70f50c4fbd9843562",
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload
  );

  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: "/favicon.png",
    badge: "/favicon.png",
    data: payload.data,
    tag: payload.data.chat_id || "general",
  };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions
  );
});

// Handle notification click event
self.addEventListener("notificationclick", function (event) {
  console.log(
    "[firebase-messaging-sw.js] Notification click occurred: ",
    event
  );

  const clickedNotification = event.notification;
  clickedNotification.close();

  // Get data from the notification
  const chatId = clickedNotification.data.chat_id;
  const orderId = clickedNotification.data.order_id;

  // Handle the click with a promise to wait for a new/existing client
  const urlToOpen = chatId
    ? `/chat/detail/${chatId}`
    : orderId
    ? `/order-detail/${orderId}`
    : "/chat";

  event.waitUntil(
    clients
      .matchAll({
        type: "window",
        includeUncontrolled: true,
      })
      .then((windowClients) => {
        // Check if there is already a window client open
        for (let i = 0; i < windowClients.length; i++) {
          const client = windowClients[i];
          if (client.url.includes(urlToOpen) && "focus" in client) {
            return client.focus();
          }
        }

        // If no open window, open a new one
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});
