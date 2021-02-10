const { getTestRecordError, getTestRecord } = require("../test-lib/test-records");
import { geoip_detect2_shortcode_evaluate_conditions } from './show-if';

const defaultRecord = getTestRecord();
const testFixture = require('../../tests/fixture_shortcode_show_if.json');



test(`Show if Test`, () => {
    testFixture.forEach((row) => {
        const result = geoip_detect2_shortcode_evaluate_conditions(row.parsed, {}, defaultRecord);
        expect(result).toBe(row.expected);
    })
});