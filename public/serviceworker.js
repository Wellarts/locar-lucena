// Service Worker Pass-Through Puro (Sem Cache)
const CACHE_NAME = "pwa-no-cache-v1";

// Força o Service Worker novo a ativar imediatamente e matar o antigo
self.addEventListener("install", event => {
    self.skipWaiting();
});

// Limpa ABSOLUTAMENTE TODOS os caches existentes no navegador deste site
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    console.log("🧹 Deletando cache permanentemente:", key);
                    return caches.delete(key);
                })
            );
        }).then(() => {
            // Assume o controle de todas as abas abertas imediatamente
            return self.clients.claim();
        })
    );
});

// Intercepta as requisições mas envia 100% direto para a rede
self.addEventListener("fetch", event => {
    // Não faz cache de nada. Apenas repassa a requisição para a internet.
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Retorna a resposta viva e atualizada do servidor Plesk
                return response;
            })
            .catch(() => {
                // Se o usuário estiver totalmente offline, deixa o navegador falhar nativamente
                // ou falhar a requisição, já que não usamos cache.
            })
    );
});