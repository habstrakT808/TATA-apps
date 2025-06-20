// Firebase Service Worker for FCM
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js"
);

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyCpgEcZuvQW70JHFbJ2mBHlM_hf8DsPWvQ",
  authDomain: "printing-commerce.firebaseapp.com",
  projectId: "printing-commerce",
  storageBucket: "printing-commerce.appspot.com",
  messagingSenderId: "244660030535",
  appId: "1:244660030535:web:ee21e126b63aa82c843562",
  measurementId: "G-MEASUREMENT_ID",
  databaseURL: "https://printing-commerce-default-rtdb.firebaseio.com",
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

  // Customize notification here
  const notificationTitle = payload.notification.title || "TATA Apps";
  const notificationOptions = {
    body: payload.notification.body || "Ada pemberitahuan baru untuk Anda!",
    icon: "/icons/Icon-192.png",
    badge: "/icons/Icon-192.png",
    tag: payload.data?.chat_id || "general",
    data: payload.data || {},
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// Notification click event
self.addEventListener("notificationclick", (event) => {
  console.log("[firebase-messaging-sw.js] Notification clicked");

  event.notification.close();

  // Handle notification click
  const urlToOpen = event.notification.data?.url || "/";
  const chatId = event.notification.data?.chat_id;

  if (chatId) {
    // Open specific chat page
    event.waitUntil(
      self.clients.matchAll({ type: "window" }).then((clientList) => {
        for (const client of clientList) {
          if (client.url.includes("/chat") && "focus" in client) {
            return client.focus();
          }
        }

        if (self.clients.openWindow) {
          return self.clients.openWindow(`/chat/${chatId}`);
        }
      })
    );
  } else {
    // Open regular URL
    event.waitUntil(
      self.clients.matchAll({ type: "window" }).then((clientList) => {
        for (const client of clientList) {
          if (client.url === urlToOpen && "focus" in client) {
            return client.focus();
          }
        }

        if (self.clients.openWindow) {
          return self.clients.openWindow(urlToOpen);
        }
      })
    );
  }
});
