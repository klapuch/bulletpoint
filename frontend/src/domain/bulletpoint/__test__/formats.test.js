import { withComparisons } from '../formats';

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
