const CACHE_NAME = "padayon-therapist-v1";

self.addEventListener("install", (event) => {
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    event.waitUntil(Promise.all([self.clients.claim()]));
});
