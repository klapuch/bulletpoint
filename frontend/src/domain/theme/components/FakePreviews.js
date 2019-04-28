// @flow
import React from 'react';
import { range } from 'lodash';
import FakePreview from './FakePreview';

type Props = {|
  +children: number,
  +show?: boolean,
|};
export default function ({ children, show = true }: Props) {
  if (!show) {
    return null;
  }
  return (
    <>
      {range(children).map(number => (
        <React.Fragment key={number}>
          <FakePreview />
          <hr />
        </React.Fragment>
      ))}
    </>
  );
}
