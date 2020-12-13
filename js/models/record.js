
import _ from '../lodash.custom';


const _get_localized = function(ret, locales) {
    if (typeof(ret) == 'object' && typeof(ret.names) == 'object') {
        if (typeof(locales) == 'string') {
            locales = [ locales ];
        }

        for (let locale of locales) {
            if (ret.names[locale]) {
                return ret.names[locale];
            }
        }
        
        return '';
    }
    return ret;
}

export const camelToUnderscore = function(key) {
    return key.replace(/([A-Z])/g, "_$1").toLowerCase();
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
        prop = camelToUnderscore(prop);

        // Treat pseudo-property 'name' as if it never existed
        if (prop.substr(-5) === '.name') {
            prop = prop.substr(0, prop.length - 5);
        }

        let ret = _.get(this.data, prop, default_value);

        // Localize property, if possible
        ret = _get_localized(ret, locales);

        return ret;
    }
    
    /**
     * Get error message, if any
     * @return string Error Message
     */
    error() {
        return _.get(this.data, 'extra.error', '');
    }
}

export default Record;