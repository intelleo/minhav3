const CACHE_NAME = "minha-cache-v2";
const ASSETS_TO_CACHE = [
  "/",
  "/css/output.css",
  "/css/custom.css",
  "/fonts/remixicon.css",
  "/vendor/axios.min.js",
  "/js/axios-setup.js",
  "/js/alerts.js",
  "/js/spa-router.js",
  "/img/icon-chat.png",
  "/img/icon-chat.webp",
  "/manifest.webmanifest",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_TO_CACHE))
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys.map((key) =>
            key !== CACHE_NAME ? caches.delete(key) : undefined
          )
        )
      )
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const request = event.request;
  if (request.method !== "GET") return;

  const url = new URL(request.url);
  const isSameOrigin = url.origin === self.location.origin;

  const isStaticAsset = (pathname) =>
    /\.(?:css|js|png|webp|jpg|jpeg|gif|svg|ico|woff2?|ttf|eot)$/.test(pathname);

  const isApiOrDynamic = (pathname) =>
    pathname.startsWith("/Mading") ||
    pathname.startsWith("/Dashboard") ||
    pathname.startsWith("/Chatbot") ||
    pathname.startsWith("/login") ||
    pathname.startsWith("/logout") ||
    pathname.startsWith("/user") ||
    pathname.startsWith("/api");

  // Navigation (HTML) -> network-first
  if (request.mode === "navigate") {
    event.respondWith(fetch(request).catch(() => caches.match("/")));
    return;
  }

  // API/dinamis atau cross-origin -> bypass SW
  if (!isSameOrigin || isApiOrDynamic(url.pathname)) {
    return;
  }

  // Static assets -> cache-first lalu update di background
  if (isStaticAsset(url.pathname) || ASSETS_TO_CACHE.includes(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cached) => {
        const networkFetch = fetch(request)
          .then((response) => {
            if (
              response &&
              response.status === 200 &&
              response.type === "basic"
            ) {
              const copy = response.clone();
              caches
                .open(CACHE_NAME)
                .then((cache) => cache.put(request, copy))
                .catch(() => {});
            }
            return response;
          })
          .catch(() => cached);
        return cached || networkFetch;
      })
    );
  }
});
