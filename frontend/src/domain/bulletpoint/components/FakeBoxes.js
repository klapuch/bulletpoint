// @flow
import { range } from 'lodash';
import React from 'react';
import FakeBox from './FakeBox';

type Props = {|
  +children: number,
  +isEmpty: boolean,
|};
export default function ({ children, isEmpty }: Props) {
  if (isEmpty) {
    return null;
  }
  return (
    <ul className="list-group">
      {range(children).map(number => <FakeBox key={number} />)}
    </ul>
  );
}
