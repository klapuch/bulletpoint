// @flow
import { range } from 'lodash';
import React from 'react';
import FakeBox from './FakeBox';

type Props = {|
  +children: number,
  +show: boolean,
|};
export default function ({ children, show }: Props) {
  if (!show) {
    return null;
  }
  return (
    <ul className="list-group">
      {range(children).map(number => <FakeBox key={number} />)}
    </ul>
  );
}
