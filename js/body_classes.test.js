/**
 * @jest-environment jsdom
 */

import { getTestRecord, getTestRecordError } from "./test-lib/test-records";
import { add_classes_to_body, calc_classes } from "./body_classes";
import Record from "./models/record";
import { set_override_with_merge } from "./lookup/override";
import { options } from './lookup/get_info';
import { domReady, isUnitTesting } from "./lib/html";
import { get_info_stored_locally_record } from "./lookup/storage";

const emptyRecord = new Record();
const defaultRecord = getTestRecord();
const errorRecord = getTestRecordError();

test('calc_classes', () => {
    expect(calc_classes(defaultRecord)).toStrictEqual({
        "continent": "EU",
        "country": "DE",
        "country-is-in-european-union": false, /* because the test data set has EU not set yet */
        "province": "HE",
        "city": "Eschborn",
    });
    expect(calc_classes(errorRecord)).toStrictEqual({
        "continent": "",
        "country": "",
        "country-is-in-european-union": false,
        "province": "",
        "city": "",
    });
    expect(calc_classes(emptyRecord)).toStrictEqual({
        "continent": "",
        "country": "",
        "country-is-in-european-union": false,
        "province": "",
        "city": "",
    });
});

const waitSomeTime = time => new Promise(resolve => {
    setTimeout(resolve, time);
});

test('body css_classes with override', async () => {
    expect(isUnitTesting()).toBe(true);

    const body = document.getElementsByTagName('body')[0];
    
    add_classes_to_body(defaultRecord);

    expect(body.classList.contains('geoip-country-DE')).toBe(true);
    expect(body.classList.contains('geoip-country-FR')).toBe(false);

    // No automatic body classes
    options.do_body_classes = false;
    set_override_with_merge('country.iso_code', 'FR');
    await waitSomeTime(100);

    let record = get_info_stored_locally_record();

    expect(record.get_country_iso()).toBe('fr');
    expect(body.classList.contains('geoip-country-DE')).toBe(true);
    expect(body.classList.contains('geoip-country-FR')).toBe(false);

    // Body classes should be automatic
    options.do_body_classes = true;
    set_override_with_merge('country.iso_code', 'IT');
    await waitSomeTime(100);

    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('it');

    expect(body.classList.contains('geoip-country-DE')).toBe(false);
    expect(body.classList.contains('geoip-country-FR')).toBe(false);
    expect(body.classList.contains('geoip-country-IT')).toBe(true);
});

test('ddd body css_classes with reevaluate test', async () => {
    const body = document.getElementsByTagName('body')[0];

    add_classes_to_body(defaultRecord);

    expect(body.classList.contains('geoip-country-DE')).toBe(true);
    expect(body.classList.contains('geoip-country-FR')).toBe(false);

    // Body classes should be skipped
    set_override_with_merge('country.iso_code', 'CZ', { reevaluate: false });
    await waitSomeTime(100);

    let record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('cz');
    expect(body.classList.contains('geoip-country-DE')).toBe(true);
    expect(body.classList.contains('geoip-country-CZ')).toBe(false);

    // Body classes should now be evaluated even though there is no change in data (because the change of CZ was not applied yet)
    set_override_with_merge('country.iso_code', 'CZ', { reevaluate: true });
    await waitSomeTime(100);

    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('cz');
    expect(body.classList.contains('geoip-country-DE')).toBe(false);
    expect(body.classList.contains('geoip-country-CZ')).toBe(true);
});