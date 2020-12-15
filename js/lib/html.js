export const domReady = new Promise(resolve => {
    if (document.readyState === "loading") {
        document.addEventListener('DOMContentLoaded', resolve);
    }
    else {
        resolve();
    }
});

export function selectItemByValue(el, value) {
    for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].value === value) {
            el.selectedIndex = i;
            break;
        }
    }
}