export function isUnitTesting() {
    return process.env.JEST_WORKER_ID !== undefined;
}

export const domReady = new Promise(resolve => {
    if (isUnitTesting()) {
        resolve();
    }

    if (document.readyState === "loading") {
        if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', resolve);
        } else {
            document.attachEvent('onreadystatechange', function () {
                if (document.readyState != 'loading') {
                    resolve();
                }
            });
        }
    } else {
        resolve();
    }
});

export function selectItemByValue(el, value) {
    for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].value === value) {
            el.selectedIndex = i;
            return true;
        }
    }
    return false;
}

/**
 * @param {*} el                Select Tag
 * @param {string} attributeName     HTML attribute name to search by
 * @param {string} attributeValue    HTML attribute value to search by
 * @returns boolean TRUE if Value found in select tag
 */
export function selectItemByAttribute(el, attributeName, attributeValue) {
    for (let i = 0; i < el.options.length; i++) {
        if (el.options[i].getAttribute(attributeName) === attributeValue) {
            el.selectedIndex = i;
            return true;
        }
    }
    return false;
}