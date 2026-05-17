// 1. MUDANÇA CRUCIAL: Controle a versão manualmente (mude para 'v2', 'v3' quando fizer deploy)
// Nunca use dynamic timestamps aqui, pois isso quebra o ciclo de vida do Service Worker.
const staticCacheName = "pwa-v1.0.0"; 

const filesToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
];

// Cache on install
self.addEventListener("install", event => {
    // Corrigido: Em Service Workers usa-se self.skipWaiting(), 'this' pode perder o contexto
    self.skipWaiting(); 
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        }).then(() => self.clients.claim()) // Força o SW atualizado a tomar conta das abas abertas imediatamente
    );
});

// Serve com estratégia correta para o Filament
self.addEventListener("fetch", event => {
    // Ignora requisições de fora do seu domínio ou requisições POST (comuns no Livewire)
    if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
        return;
    }

    event.respondWith(
        // Estratégia Network-First para rotas e arquivos do Filament / Livewire
        fetch(event.request)
            .then(networkResponse => {
                // Se a rede responder bem e for um asset estático básico, atualiza o cache em background
                if (networkResponse.status === 200 && filesToCache.includes(new URL(event.request.url).pathname)) {
                    let responseClone = networkResponse.clone();
                    caches.open(staticCacheName).then(cache => cache.put(event.request, responseClone));
                }
                return networkResponse;
            })
            .catch(() => {
                // Se a rede falhar (offline), busca no cache
                return caches.match(event.request).then(cacheResponse => {
                    return cacheResponse || caches.match('offline');
                });
            })
    );
});