
import lodash_get from 'lodash.get';


const _get_localized = function(ret, locales) {
    if (typeof(ret) == 'object' && typeof(ret.names) == 'object') {
        for (let locale of locales) {
            if (ret.names[locale]) {
                return ret.names[locale];
            }
        }
        return '';
    }
}


class Record {
    data = {};
    default_locales = [];

    constructor(data, default_locales) {
        this.data = data || {};
        this.default_locales = default_locales || ['en']; 
    }

    get(prop, default_value) {
        return this.get_with_locales(prop, this.default_locales, default_value);
    }
    
    
    get_with_locales(prop, locales, default_value) {
        // Treat pseudo-property 'name' as if it never existed
        if (prop.substring(-5) === '.name') {
            prop = prop.substring(0, -5);
        }

        let ret = lodash_get(this.data, prop, default_value);

        // Localize property, if possible
        ret = _get_localized(ret, locales);

        return ret;
    }

    

    /**
     * Get error message, if any
     * @return string Error Message
     */
    message() {
        return lodash_get(this.data, 'extra.message', '');
    }
}

export default Record;