export const domReady = new Promise(resolve => {
    if (document.readyState === "loading") {
        if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', resolve);
        } else {
            document.attachEvent('onreadystatechange', function () {
                if (document.readyState != 'loading')
                    resolve();
            });
        }
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

export function selectItemByAttribute(el, attributeName, attributeValue) {
    for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].getAttribute(attributeName) === attributeValue) {
            el.selectedIndex = i;
            break;
        }
    }
}


export function triggerNativeEvent(el, name) {
    if (document.createEvent) {
        const event = document.createEvent('HTMLEvents');
        event.initEvent(name, true, false);
        el.dispatchEvent(event);
    } else {
        el.fireEvent('on' + name);
    }
}