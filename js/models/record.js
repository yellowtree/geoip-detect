
import _ from '../lodash.custom';


const _get_localized = function(ret, locales) {
    if (typeof(ret) == 'object') {
        if (typeof(locales) == 'string') {
            locales = [ locales ];
        }

        if (typeof (ret.names) == 'object') {
            for (let locale of locales) {
                if (ret.names[locale]) {
                    return ret.names[locale];
                }
            }
        }

        if (ret.name) {
            return ret.name;
        }
    }
    return ret;
}

export const camelToUnderscore = function(key) {
    // Tolerate PascalCase. But _key stays _key [ (?<=[a-z0-9]) means Look-ahead]
    return key.replace(/(?<=[a-z0-9])([A-Z])/g, "_$1").toLowerCase();
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

        if (typeof (ret) !== 'string' && typeof (ret) !== 'undefined' ) {
            console.warn('Geolocation IP Detection: The property "' + prop + '" is of type "' + typeof (ret) + '", should be string', {property: prop, value: ret})
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
     * Get error message, if any
     * @return string Error Message
     */
    error() {
        return _.get(this.data, 'extra.error', '');
    }
}

export default Record;