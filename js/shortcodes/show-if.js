import { get_options } from './helpers';
import _intersect from 'just-intersect';


export function do_shortcode_show_if(el, record) {
    const opt = get_options(el);
    const evaluated = geoip_detect2_shortcode_evaluate_conditions(opt.parsed, opt, record);

    if (!evaluated) {
        el.style.display = "none";
        el.classList.add('geoip-hidden');
        el.classList.remove('geoip-shown');
    } else {
        el.style.display = '';
        el.classList.remove('geoip-hidden');
        el.classList.add('geoip-shown');
    }
}

export function geoip_detect2_shortcode_evaluate_conditions(parsed, opt, record) {
    const alternativePropertyNames = [
        'name',
        'iso_code',
        'iso_code3',
        'code',
        'geoname_id',
    ];

    let isConditionMatching = (parsed.op === 'or') ? false : true;

    parsed.conditions.forEach(c => {
        let subConditionMatching = false;
        let values = [];

        const raw_value = record.get_raw(c.p);

        if (raw_value === null) {
            subConditionMatching = false;
        } else {
            if (typeof (raw_value) === 'object') {
                alternativePropertyNames.forEach(name => {
                    if (raw_value[name]) {
                        values.push(raw_value[name]);
                    } else if (name == 'name') {
                        values.push(record.get_with_locales(c.p, opt.lang));
                    }
                })
            } else {
                values = [raw_value]
            }
        }

        subConditionMatching = geoip_detect2_shortcode_check_subcondition(c.v, values);

        if (c.not) {
            subConditionMatching = !subConditionMatching;
        }

        if (parsed.op === 'or') {
            isConditionMatching = isConditionMatching || subConditionMatching;
        } else {
            isConditionMatching = isConditionMatching && subConditionMatching;
        }

    });

    if (parsed.not) {
        isConditionMatching = !isConditionMatching;
    }

    return isConditionMatching;
}

function geoip_detect2_shortcode_check_subcondition(expectedValues, actualValues) {
    if (actualValues[0] === true) {
        actualValues = ['true', 'yes', 'y', '1'];
    } else if (actualValues[0] === false) {
        actualValues = ['false', 'no', 'n', '0', ''];
    }

    actualValues = actualValues.map(x => String(x).toLowerCase())

    expectedValues = expectedValues.split(',');
    if (expectedValues.indexOf('') !== -1) {
        if (actualValues.length === 0) {
            return true;
        }
    }

    const intersect = _intersect(expectedValues, actualValues);

    return intersect.length > 0;
}