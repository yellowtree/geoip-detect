export const domReady = new Promise(resolve => {
    if (document.readyState === "loading") {
        document.addEventListener('DOMContentLoaded', resolve);
    }
    else {
        resolve();
    }
});
console.info(domReady);