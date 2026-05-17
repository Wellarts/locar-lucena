// 1. Forçamos uma nova versão estática toda vez que alteramos o sistema.
// Mude para 'pwa-v2', 'pwa-v3', etc., quando fizer modificações grandes.
const staticCacheName = "pwa-v1.0.1"; 

// Mantemos apenas o essencial para a estrutura do PWA funcionar offline básico.
// NUNCA coloque rotas dinâmicas do Filament aqui.
const filesToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
];

// O evento 'install' roda assim que o navegador detecta qualquer alteração neste arquivo
self.addEventListener("install", event => {
    // Força o novo Service Worker a se tornar o ativo imediatamente,
    // matando o "Service Worker zumbi" antigo que estava segurando o cache.
    self.skipWaiting(); 
    
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    );
});

// O evento 'activate' limpa a bagunça antiga assim que o novo assume o controle
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    // Filtra e deleta TODOS os caches antigos que começam com 'pwa-'
                    // Inclusive os criados dinamicamente com o antigo getTime()
                    .filter(cacheName => (cacheName.startsWith("pwa-") && cacheName !== staticCacheName))
                    .map(cacheName => {
                        console.log("🧹 Removendo cache antigo expirado:", cacheName);
                        return caches.delete(cacheName);
                    })
            );
        }).then(() => {
            // Reivindica o controle das abas abertas imediatamente sem precisar dar F5
            return self.clients.claim();
        })
    );
});

// O evento 'fetch' intercepta as requisições (Aqui corrigimos o problema do Filament)
self.addEventListener("fetch", event => {
    // 1. Ignorar requisições que não sejam GET (Filament/Livewire usam muito POST/PUT)
    // 2. Ignorar requisições para extensões do navegador ou domínios externos
    if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // 3. Ignorar caminhos administrativos do Filament (evita travar selects e tabelas em cache)
    if (event.request.url.includes('/admin') || event.request.url.includes('/livewire')) {
        return; // Deixa passar direto para a rede, sem tocar no cache
    }

    event.respondWith(
        // Estratégia Network-First (Tenta a rede primeiro para garantir o arquivo novo)
        fetch(event.request)
            .then(networkResponse => {
                // Se o arquivo veio da rede com sucesso e está na lista de assets básicos, atualiza o cache
                if (networkResponse.status === 200) {
                    const url = new URL(event.request.url);
                    if (filesToCache.includes(url.pathname)) {
                        let responseClone = networkResponse.clone();
                        caches.open(staticCacheName).then(cache => cache.put(event.request, responseClone));
                    }
                }
                return networkResponse;
            })
            .catch(() => {
                // Se a rede falhar completamente (usuário ficou sem internet), recorre ao cache
                return caches.match(event.request).then(cacheResponse => {
                    return cacheResponse || caches.match('/');
                });
            })
    );
});     