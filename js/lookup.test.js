/**
 * @jest-environment jsdom
 */

import { set_override, get_info } from "./lookup";
import { getTestRecord } from "./test-lib/test-records";
import Record from "./models/record";

const defaultRecord = getTestRecord();
const emptyRecord = new Record();

test('override', async () => {
    let record;

    set_override(defaultRecord);
    record = await get_info();

    expect(record.get_country_iso()).toBe('de');

    set_override(emptyRecord);
    record = await get_info();

    expect(record.get_country_iso()).toBe('');

    set_override({country:{iso_code: 'fr'}});
    record = await get_info();

    expect(record.get_country_iso()).toBe('fr');

    set_override({ });
    record = await get_info();

    expect(record.get_country_iso()).toBe('');
})

test('override data', async () => {
    let record;

    set_override({ country: { iso_code: 'fr' } });
    record = await get_info();
    expect(record.get_country_iso()).toBe('fr');

    set_override({});
    record = await get_info();
    expect(record.get_country_iso()).toBe('');

    set_override({ country: { iso_code: 'ru' } });
    record = await get_info();
    expect(record.get_country_iso()).toBe('ru');

    set_override();
    record = await get_info();
    expect(record.get_country_iso()).toBe('');
});

test('warning if negative duration', () => {
    const spy = jest.spyOn(console, 'warn').mockImplementation(() => { })

    const ret = set_override({ country: { iso_code: 'fr' } }, -4);
    expect(ret).toBe(false);

    expect(spy).toHaveBeenCalled();
    spy.mockRestore();
});