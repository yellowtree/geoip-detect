
let _internalEvent = false;
export function isInternalEvent() {
    return _internalEvent;
}

export function triggerNativeEvent(el, eventName, options = null) {
    _internalEvent = true;

    let event;
    if (window.CustomEvent && typeof window.CustomEvent === 'function') {
        event = new CustomEvent(eventName, {detail : options});
    } else {
        // Compat for IE
        event = document.createEvent('CustomEvent');
        event.initCustomEvent(eventName, true, true, options);
    }
    el.dispatchEvent(event);

    _internalEvent = false;
}