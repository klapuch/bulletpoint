import { getById } from '../selects';

test('getting by ID with full related themes', () => {
  expect(getById(1, {
    theme: {
      single: {
        1: {
          payload: {
            name: 'abc',
            related_themes_id: [2],
          },
        },
        2: {
          payload: {
            name: 'def',
            related_themes_id: [],
          },
        },
      },
    },
  })).toEqual(
    { name: 'abc', related_themes: [{ name: 'def', related_themes_id: [] }], related_themes_id: [2] },
  );
});

test('getting by ID related to each other', () => {
  expect(getById(1, {
    theme: {
      single: {
        1: {
          payload: {
            name: 'abc',
            related_themes_id: [2],
          },
        },
        2: {
          payload: {
            name: 'def',
            related_themes_id: [1],
          },
        },
      },
    },
  })).toEqual(
    { name: 'abc', related_themes: [{ name: 'def', related_themes_id: [1] }], related_themes_id: [2] },
  );
});
