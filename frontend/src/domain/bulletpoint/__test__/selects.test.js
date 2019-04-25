import {
  fetchedAll, relatedThemesFetching, withChildrenGroups, orderByExpandBulletpoint,
} from '../selects';

test('ordering by expand - first root', () => {
  expect(orderByExpandBulletpoint(
    [
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
    ],
    1,
  )).toEqual(
    [
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
    ],
  );
});

test('ordering by expand - root between', () => {
  expect(orderByExpandBulletpoint(
    [
      { id: 5, group: { root_bulletpoint_id: null } },
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
    ],
    1,
  )).toEqual(
    [
      { id: 5, group: { root_bulletpoint_id: null } },
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
    ],
  );
});

test('ordering by expand - nullable expand', () => {
  expect(orderByExpandBulletpoint(
    [
      { id: 5, group: { root_bulletpoint_id: null } },
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
    ],
    null,
  )).toEqual(
    [
      { id: 5, group: { root_bulletpoint_id: null } },
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: 1 } },
      { id: 4, group: { root_bulletpoint_id: null } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
    ],
  );
});

test('empty as not fetchedAll', () => {
  expect(fetchedAll(1, { themeBulletpoints: { 1: { payload: {} } } })).toBe(true);
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

test('from parents to children', () => {
  expect(
    withChildrenGroups([
      { id: 1, group: { root_bulletpoint_id: null } },
      { id: 2, group: { root_bulletpoint_id: null } },
      { id: 3, group: { root_bulletpoint_id: 1 } },
      { id: 5, group: { root_bulletpoint_id: 1 } },
    ]),
  ).toEqual([
    {
      id: 1,
      group: {
        root_bulletpoint_id: null,
        children_bulletpoints: [
          { id: 3, group: { root_bulletpoint_id: 1 } },
          { id: 5, group: { root_bulletpoint_id: 1 } },
        ],
      },
    },
    { id: 2, group: { root_bulletpoint_id: null, children_bulletpoints: [] } },
  ]);
});
