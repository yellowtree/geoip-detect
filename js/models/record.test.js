import { getTestRecord, getTestRecordError } from "../test-lib/test-records";
import Record from "./record";


const emptyRecord = new Record();
const defaultRecord = getTestRecord();
const errorRecord = getTestRecordError();

test('get error message', () => {
    expect(emptyRecord.error()).toBe('');
    expect(defaultRecord.error()).toBe('');
    expect(errorRecord.error()).not.toBe('');
})

test('localisation variants', () => {
    expect(defaultRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.names.de', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.names.en', ['de', 'en'])).toBe('Germany');

    expect(defaultRecord.get_with_locales('most_specific_subdivision', ['de', 'en'])).toBe('Hessen');
    expect(defaultRecord.get_with_locales('mostSpecificSubdivision', ['de', 'en'])).toBe('Hessen');
});

test('localisation', () => {
    expect(defaultRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.name', ['en', 'de'])).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name', ['nn', 'mm', 'de', 'en'])).toBe('Deutschland');
});

test('country iso', () => {
    expect(defaultRecord.get_country_iso()).toBe('de');
    expect(emptyRecord.get_country_iso()).toBe('');
    expect(errorRecord.get_country_iso()).toBe('');
});