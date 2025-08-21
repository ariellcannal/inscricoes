const STATIC_CACHE = 'cannal-inscricoes-static-v1';
const DYNAMIC_CACHE = 'cannal-inscricoes-dynamic-v1';

const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/writable/logos/CANNAL_SIMB_POS192x192.png',
  '/favicon.ico',
  '/assets/css/app.css',   // ajuste se houver outros
  '/assets/css/bootstrap.min.css',   // ajuste se houver outros
  '/assets/css/xcrud.css',   // ajuste se houver outros
  '/assets/css/login.css',   // ajuste se houver outros
  '/assets/js/app.js',        // ajuste se houver outros
  '/assets/js/alunos.js',        // ajuste se houver outros
  '/assets/js/inscricoes_admin.js',        // ajuste se houver outros
  '/assets/js/inscricoes_aluno.js',        // ajuste se houver outros
  '/assets/js/login.js',        // ajuste se houver outros
  '/assets/js/meta.js',        // ajuste se houver outros
  '/assets/js/recebiveis.js',        // ajuste se houver outros
  '/assets/js/repasses.js',        // ajuste se houver outros
  '/assets/js/transacoes.js'        // ajuste se houver outros
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then(cache => cache.addAll(STATIC_ASSETS))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.map(key => {
          if (key !== STATIC_CACHE && key !== DYNAMIC_CACHE) {
            return caches.delete(key);
          }
        })
      )
    )
  );
});

self.addEventListener('fetch', event => {
  const req = event.request;
  const isSameOrigin = new URL(req.url).origin === self.location.origin;

  if (req.method !== 'GET' || !isSameOrigin) {
    return;
  }

  event.respondWith(
    caches.match(req).then(cached => {
      return (
        cached ||
        fetch(req).then(fetchRes => {
          return caches.open(DYNAMIC_CACHE).then(cache => {
            cache.put(req, fetchRes.clone());
            return fetchRes;
          });
        })
      );
    })
  );
});
