import _get from 'just-safe-get';


const _get_localized = function(ret, locales) {
    if (typeof(ret) === 'object' && ret !== null) {
        if (typeof (ret.names) === 'object' && typeof (locales) === 'object') {
            for (let i = 0 ; i < locales.length ; i++) {
                let locale = locales[i];

                if (ret.names[locale]) {
                    return ret.names[locale];
                }
            }
        }

        if (ret.name) {
            return ret.name;
        }

        return '';
    }
    return ret;
}

export const camelToUnderscore = function(key) {
    key = key.split('.').map((x) => {
        if (typeof (x) !== 'string' || typeof (x[0]) !== 'string') {
            return '';
        }
        x = x[0].toLowerCase() + x.slice(1); // to allow "MostSpecificSubdivision"
        x = x.replace(/([A-Z])/g, "_$1").toLowerCase();
        return x;
    }).join('.');

    return key;
}

class Record {
    data = {};
    default_locales = [];

    constructor(data, default_locales) {
        this.data = data || { is_empty: true };
        
        this.default_locales = ['en']; 
        this.default_locales = this._process_locales(default_locales);
    }

    get(prop, default_value) {
        return this.get_with_locales(prop, null, default_value);
    }

    get_raw(prop) {
        prop = camelToUnderscore(prop);
        return _get(this.data, prop, null);
    }
    
    has_property(prop) {
        const ret = this._lookup_with_locales(prop, this.default_locales, null)
        return ret !== null;
    }

    _lookup_with_locales(prop, locales, default_value = '') {
        locales = this._process_locales(locales);

        // Treat pseudo-property 'name' as if it never existed
        if (prop.substr(-5) === '.name') {
            prop = prop.substr(0, prop.length - 5);
        }

        let ret = this.get_raw(prop);
        
        // Localize property, if possible
        ret = _get_localized(ret, locales);
        
        if (ret === null || ret === '') {
            ret = default_value;
        }

        return ret;
    }
    
    _process_locales(locales) {
        if (typeof(locales) === 'string') {
            locales = [ locales ];
        }
        if (!Array.isArray(locales) || locales.length === 0) {
            locales = this.default_locales;
        }
        return locales;
    }

    get_with_locales(prop, locales, default_value) {
        const ret = this._lookup_with_locales(prop, locales, default_value);

        if (typeof(ret) === 'object') {
            console.warn('Geolocation IP Detection: The property "' + prop + '" is of type "' + typeof (ret) + '", should be string or similar', ret)
        }
        if (typeof(ret) === 'undefined') {
            console.warn('Geolocation IP Detection: The property "' + prop + '" is not defined, please check spelling or maybe you need a different data source', { data: this.data })
            return '';
        }

        return ret;
    }

    get_country_iso() {
        let country = this.get('country.iso_code');
        if(country) {
            country = country.substr(0, 2).toLowerCase();
        }
        return country;
    }

    /**
     * Check if there is information available for this IP
     * @returns boolean 
     */
    is_empty() {
        return this.get('is_empty', false);
    }
    
    /**
     * Get error message, if any
     * @return string Error Message
     */
    error() {
        return this.get_raw('extra.error') || '';
    }

    /**
     * Get the raw data of this object
     * @returns object
     */
    serialize() {
        return this.data;
    }
}

export default Record;