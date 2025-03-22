const CACHE_NAME = "my-todo-cache-v1";
const urlsToCache = [
  "https://mytodo.f5.si",
  "https://mytodo.f5.silogin.css",
  "https://mytodo.f5.siicon512.png",
  "https://mytodo.f5.siicon192.png"
];

// Install Service Worker
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log("Opened cache");
      return cache.addAll(urlsToCache);
    })
  );
});

// Fetch Cached Resources
self.addEventListener("fetch", event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});

// Update Cache
self.addEventListener("activate", event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// プッシュ通知を受け取ったときのイベント
self.addEventListener('push', function (event) {
    const title = 'test';
    const options = {
        body: event.data.text(), // サーバーからのメッセージ
        tag: title, // タイトル
        icon: 'icon512.png', // アイコン
        badge: 'icon512.png' // アイコン
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// プッシュ通知をクリックしたときのイベント
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    event.waitUntil(
        // プッシュ通知をクリックしたときにブラウザを起動して表示するURL
        clients.openWindow('https://mytodo.f5.si')
    );
});