
let _internalEvent = false;
export function isInternalEvent() {
    return _internalEvent;
}

export function triggerNativeEvent(el, name) {
    _internalEvent = true;
    if (document.createEvent) {
        const event = document.createEvent('HTMLEvents');
        event.initEvent(name, true, false);
        el.dispatchEvent(event);
    } else {
        el.fireEvent('on' + name);
    }
    _internalEvent = false;
}