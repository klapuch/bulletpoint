import { withComparisons, replaceMatches } from '../formats';

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
