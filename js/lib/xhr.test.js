import { jsonDecodeIfPossible } from "./xhr";

test('jsonDecode', () => {
    expect(jsonDecodeIfPossible('{"is_empty":true}')).toStrictEqual({is_empty:true});
    expect(jsonDecodeIfPossible('{"is_empty":true')).toStrictEqual({ is_empty: true, extra: { error: 'Invalid JSON: {"is_empty":true'} });
});