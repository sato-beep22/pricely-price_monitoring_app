self.addEventListener("install", event => {
    console.log("Service worker installed");
});

self.addEventListener("fetch", event => {
    // Basic fetch handler (network first, no complex offline caching to start)
});
