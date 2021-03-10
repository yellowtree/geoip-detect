import { getTestRecord, getTestRecordError } from "../test-lib/test-records";
import Record, { camelToUnderscore } from "./record";


const emptyRecord = new Record();
const defaultRecord = getTestRecord();
const errorRecord = getTestRecordError();

test('serialize', () => {
    const testData = {a: '1', c: { names: {de: 'b', en: 'a'} } };
    const testRecord = new Record(testData)
    expect(testRecord.serialize()).toStrictEqual(testData);
    expect(testRecord.get_with_locales('c')).toBe('a');
    expect(emptyRecord.serialize()).toStrictEqual({'is_empty': true});
});

test('get error message', () => {
    expect(emptyRecord.error()).toBe('');
    expect(defaultRecord.error()).toBe('');
    expect(errorRecord.error()).not.toBe('');
})

test('get with default', () => {
    expect(defaultRecord.get('')).toBe('');
    expect(defaultRecord.get('', 'default')).toBe('default');
    expect(defaultRecord.get('xyz', 'default')).toBe('default');
    expect(defaultRecord.get('city.xyz', 'default')).toBe('default');
    expect(defaultRecord.get('city', 'default')).toBe('Eschborn');
});

test('is_empty', () => {
    expect(defaultRecord.get('is_empty', 'default')).toBe(false);
    expect(emptyRecord.get('is_empty', 'default')).toBe(true);
    expect(errorRecord.get('is_empty', 'default')).toBe(true);
    
    expect(defaultRecord.is_empty()).toBe(false);
    expect(emptyRecord.is_empty()).toBe(true);
    expect(errorRecord.is_empty()).toBe(true);
})

test('localisation variants', () => {
    expect(defaultRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.names.de', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.names.en', ['de', 'en'])).toBe('Germany');
    
    expect(defaultRecord.get_with_locales('most_specific_subdivision', ['de', 'en'])).toBe('Hessen');
    
    const otherRecord = new Record({country: {name: 'Deutschland'}});
    expect(otherRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    
    expect(defaultRecord.get_with_locales('extra', ['de', 'en'])).toBe('');
    expect(defaultRecord.get_with_locales('xyz.name', ['de', 'en'])).toBe('');
    expect(otherRecord.get_with_locales('city.name', ['de', 'en'])).toBe('');

    expect(defaultRecord.get_with_locales('country.name', 'de')).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.name', [])).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name', null)).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name', undefined)).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name')).toBe('Germany');
    expect(defaultRecord.get('country.name')).toBe('Germany');
});

test('localisation', () => {
    expect(defaultRecord.get_with_locales('country.name', ['de', 'en'])).toBe('Deutschland');
    expect(defaultRecord.get_with_locales('country.name', ['en', 'de'])).toBe('Germany');
    expect(defaultRecord.get_with_locales('country.name', ['nn', 'mm', 'de', 'en'])).toBe('Deutschland');
});
test('localisation with defaults', () => {
    expect(defaultRecord.get_with_locales('unknownAttribute', ['en'], 'default')).toBe('default');
    expect(defaultRecord.get_with_locales('country.name', ['xs'])).toBe('');
    expect(defaultRecord.get_with_locales('country.name', ['xs'], 'default')).toBe('default');
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

test('has_property', () => {
    expect(defaultRecord.has_property('country')).toBe(true);
    expect(emptyRecord.has_property('country')).toBe(false);
    expect(defaultRecord.has_property('xyz')).toBe(false);
    expect(defaultRecord.has_property('is_empty')).toBe(true);
    expect(emptyRecord.has_property('is_empty')).toBe(true);

    expect(defaultRecord.has_property('country.name')).toBe(true);
    expect(emptyRecord.has_property('country')).toBe(false);
});