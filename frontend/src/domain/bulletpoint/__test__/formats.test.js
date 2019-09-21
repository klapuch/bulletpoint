import { withComparisons, replaceMatches, withoutMatches } from '../formats';

test('adding than part', () => {
  expect(
    withComparisons(
      'This is better',
      {
        compared_theme: [
          { id: 1, name: 'everything' },
        ],
      },
    ),
  ).toMatchSnapshot();

  expect(
    withComparisons(
      'This is better',
      {
        compared_theme: [
          { id: 1, name: 'everything' },
          { id: 2, name: 'else' },
        ],
      },
    ),
  ).toMatchSnapshot();
});

test('without than part for empty', () => {
  expect(
    withComparisons(
      'This good enough',
      { compared_theme: [] },
    ),
  ).toMatchSnapshot();
});

test('replacing by positions', () => {
  expect(
    replaceMatches(
      {
        content: '[[PHP]] is [[acronym]]',
        referenced_theme: [
          { id: 1, name: 'PHP' },
          { id: 2, name: 'Acronym' },
        ],
      },
    ),
  ).toMatchSnapshot();
});

test('text as nothing to replae', () => {
  expect(
    replaceMatches(
      {
        content: 'PHP is acronym',
      },
    ),
  ).toMatchSnapshot();
});

test('without matches', () => {
  expect(withoutMatches('[[PHP]] is great')).toEqual('PHP is great');
  expect(withoutMatches('[[PHP]] is great once [[again]]')).toEqual('PHP is great once again');
  expect(withoutMatches('[[PHP]]')).toEqual('PHP');
  expect(withoutMatches('PHP is great')).toEqual('PHP is great');
  expect(withoutMatches('')).toEqual('');
  expect(withoutMatches(null)).toEqual(null);
});
