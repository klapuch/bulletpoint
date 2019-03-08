import React from 'react';
import renderer from 'react-test-renderer';
import Star from '../Star';

test('renders active star', () => {
  const component = renderer.create(
    <Star active onClick={() => null} />,
  );
  const tree = component.toJSON();
  expect(tree).toMatchSnapshot();
});

test('renders not active star', () => {
  const component = renderer.create(
    <Star active={false} onClick={() => null} />,
  );
  const tree = component.toJSON();
  expect(tree).toMatchSnapshot();
});
