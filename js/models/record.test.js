import { getTestRecord, getTestRecordError } from "../test-lib/test-records";
import Record, { camelToUnderscore } from "./record";


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
});

test('localisation', () => {
    expect(defaultRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.name', ['en', 'de'])).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name', ['nn', 'mm', 'de', 'en'])).toBe('Deutschland');
});

test('camelCase', () => {
    expect(camelToUnderscore('_specific_subdivision')).toBe('_specific_subdivision');
    expect(camelToUnderscore('MostSpecificSubdivision')).toBe('most_specific_subdivision');
    expect(camelToUnderscore('mostSpecificSubdivision')).toBe('most_specific_subdivision');
    expect(camelToUnderscore('mostSpecificSubdivision.isoCode')).toBe('most_specific_subdivision.iso_code');
    expect(camelToUnderscore('most_specific_subdivision')).toBe('most_specific_subdivision');
    expect(camelToUnderscore('country.iso_code')).toBe('country.iso_code');
    expect(camelToUnderscore('country.iso_code.0')).toBe('country.iso_code.0');
    expect(camelToUnderscore('Country.IsoCode')).toBe('country.iso_code');
});

test('country iso', () => {
    expect(defaultRecord.get_country_iso()).toBe('de');
    expect(emptyRecord.get_country_iso()).toBe('');
    expect(errorRecord.get_country_iso()).toBe('');
});