let CACHE_NAME="demo-cache"
let FILES_TO_CACHE=[
    "/",
    "index.html",
    "index.css",
    "index.js"
]

self.addEventListener("install",function(event){
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache){
            return cache.addAll(FILES_TO_CACHE)
        })
    )
})

self.addEventListener("fetch",function(event){
    event.respondWith(
        caches.match(event.request).then(function(response){
            return response!=null?response:fetch(event.request)
        })
    )
})