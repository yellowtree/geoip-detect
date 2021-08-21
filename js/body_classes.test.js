/**
 * @jest-environment jsdom
 */

import { getTestRecord, getTestRecordError } from "./test-lib/test-records";
import { add_classes_to_body, calc_classes } from "./body_classes";
import Record from "./models/record";
import { get_info_stored_locally_record, options, set_override, set_override_with_merge } from "./lookup";

const emptyRecord = new Record();
const defaultRecord = getTestRecord();
const errorRecord = getTestRecordError();

test('calc_classes', () => {
    expect(calc_classes(defaultRecord)).toStrictEqual({
        "continent": "EU",
        "country": "DE",
        "country-is-in-european-union": false, /* because the test data set has EU not set yet */
        "province": "HE",
    });
    expect(calc_classes(errorRecord)).toStrictEqual({
        "continent": "",
        "country": "",
        "country-is-in-european-union": false,
        "province": "",
    });
    expect(calc_classes(emptyRecord)).toStrictEqual({
        "continent": "",
        "country": "",
        "country-is-in-european-union": false,
        "province": "",
    });
});

test('css_classes', () => {
    const body = document.getElementsByTagName('body')[0];
    
    add_classes_to_body(defaultRecord);

    expect(body.classList.contains('geoip-country-DE')).toBe(true);
    expect(body.classList.contains('geoip-country-FR')).toBe(false);

    set_override_with_merge('country.iso_code', 'FR');
    let record = get_info_stored_locally_record();
    add_classes_to_body(record); 

    expect(body.classList.contains('geoip-country-FR')).toBe(true);
    expect(body.classList.contains('geoip-country-DE')).toBe(false);
});