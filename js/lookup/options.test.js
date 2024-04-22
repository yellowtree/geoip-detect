/**
 * @jest-environment jsdom
 */

import { options } from "./options";

test('options', () => {
    expect(options.do_body_classes).toBe(false);
});