
import { get as lodash_get } from 'lodash-es';


const _get_localized = function(ret, locales) {
    if (typeof(ret) == 'object' && typeof(ret.names) == 'object') {
        for (let locale of locales) {
            if (ret.names[locale]) {
                return ret.names[locale];
            }
        }
        return '';
    }
    return ret;
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
        if (prop.substr(-5) === '.name') {
            prop = prop.substr(0, prop.length - 5);
        }

        // TODO handle most_specific_subdivision (here or in PHP)?

        let ret = lodash_get(this.data, prop, default_value);

        // Localize property, if possible
        ret = _get_localized(ret, locales);

        return ret;
    }
    
    /**
     * Get error message, if any
     * @return string Error Message
     */
    error() {
        return lodash_get(this.data, 'extra.error', '');
    }
}

export default Record;