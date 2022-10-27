/**
 * @jest-environment jsdom
 */

import { set_override, set_override_with_merge } from "./override";
import { getTestRecord } from "../test-lib/test-records";
import Record from "../models/record";
import { get_info } from "./get_info";
import { options as globalOptions } from './get_info';
import { get_info_stored_locally_record } from "./storage";

const defaultRecord = getTestRecord();
const emptyRecord = new Record();

test('override', async () => {
    let record;

    set_override(defaultRecord);
    record = await get_info();

    expect(record.get_country_iso()).toBe('de');
    expect(record.get('extra.override')).toBe(true);

    set_override(emptyRecord);
    record = await get_info();

    expect(record.get_country_iso()).toBe('');

    set_override({ country: { iso_code: 'fr' } });
    record = get_info_stored_locally_record();

    expect(record.get_country_iso()).toBe('fr');

    set_override({});
    record = get_info_stored_locally_record();

    expect(record.get_country_iso()).toBe('');
})

test('override if empty', () => {
    localStorage.removeItem(globalOptions.cookie_name);
    set_override_with_merge('country.iso_code', 'FR');
    let record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('fr');
});

test('override data', () => {
    let record;

    set_override({ country: { iso_code: 'fr' } });
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('fr');

    set_override({});
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('');

    set_override({ country: { iso_code: 'ru' } });
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('ru');

    set_override();
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('');

    set_override_with_merge('country.iso_code', 'fr');
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('fr');
    set_override_with_merge('city.name', 'Paris');
    record = get_info_stored_locally_record();
    expect(record.get_country_iso()).toBe('fr');
    expect(record.get('city.name')).toBe('Paris');

    set_override_with_merge('a.b.c', 'd');
    record = get_info_stored_locally_record();
    expect(record.get('a.b.c')).toBe('d');
});

test('warning if negative duration', () => {
    const spy = jest.spyOn(console, 'warn').mockImplementation(() => { })

    const ret = set_override({ country: { iso_code: 'fr' } }, -4);
    expect(ret).toBe(false);

    expect(spy).toHaveBeenCalled();
    spy.mockRestore();
});