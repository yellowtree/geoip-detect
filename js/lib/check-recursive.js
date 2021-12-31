
let _listener_active = false; // for recursion detection (maybe remove later)
let _change_counter = 0; 

export function check_recursive_before() {
    _change_counter++;
    if (_listener_active || _change_counter > 10) {
        console.warn('Error: Thats weird! autosave change detected a recursion (' + _change_counter + ')! Please file a bug report about this and include the first 10 lines of the callstack below:');
        console.trace();
        if (process.env.NODE_ENV !== 'production') {
            debugger;
        }
        return false;
    }
    _listener_active = true;
    return true;
}


export function check_recursive_after() {
    _listener_active = false;
}