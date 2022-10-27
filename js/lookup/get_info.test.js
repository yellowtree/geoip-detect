/**
 * @jest-environment jsdom
 */

import { options } from "./get_info";

test('options', () => {
    options

    expect(options.do_body_classes).toBe(false);
});