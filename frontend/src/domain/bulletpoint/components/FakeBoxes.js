// @flow
import { range } from 'lodash';
import React from 'react';
import FakeBox from './FakeBox';

type Props = {|
  +children: number,
|};
export default function ({ children }: Props) {
  return (
    <ul className="list-group">
      {range(children).map(number => <FakeBox key={number} />)}
    </ul>
  );
}
