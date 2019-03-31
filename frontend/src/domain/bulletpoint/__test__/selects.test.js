import { fetchedAll, relatedThemesFetching } from '../selects';

test('empty as not fetchedAll', () => {
  expect(fetchedAll(1, { themeBulletpoints: { 1: { payload: {} } } })).toBe(false);
  expect(fetchedAll(1, { themeBulletpoints: { 1: {} } })).toBe(false);
});

test('fetchedAll as not empty', () => {
  expect(fetchedAll(1, { themeBulletpoints: { 1: { payload: { foo: 'bar' } } } })).toBe(true);
});

test('relatedThemesFetching', () => {
  expect(relatedThemesFetching({
    theme: {
      single: {
        1: { fetching: true },
        2: { fetching: true },
        3: { fetching: true },
      },
    },
  },
  [[1, 2], [3]])).toBe(true);
  expect(relatedThemesFetching({
    theme: {
      single: {
        1: { fetching: false },
        2: { fetching: true },
        3: { fetching: true },
      },
    },
  },
  [[1, 2], [3]])).toBe(true);
  expect(relatedThemesFetching({
    theme: {
      single: {
        1: { fetching: false },
        2: { fetching: false },
        3: { fetching: true },
      },
    },
  },
  [[1, 2], [3]])).toBe(true);
  expect(relatedThemesFetching({
    theme: {
      single: {
        1: { fetching: false },
        2: { fetching: false },
        3: { fetching: false },
      },
    },
  },
  [[1, 2], [3]])).toBe(false);
  expect(relatedThemesFetching({}, [])).toBe(false);
});
