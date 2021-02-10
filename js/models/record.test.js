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