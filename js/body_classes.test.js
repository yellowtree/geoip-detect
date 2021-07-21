/**
 * @jest-environment jsdom
 */

import { getTestRecord, getTestRecordError } from "./test-lib/test-records";
import { calc_classes } from "./body_classes";
import Record from "./models/record";

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