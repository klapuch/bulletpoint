// @flow
import { range } from 'lodash';
import React from 'react';
import SkeletonBox from './SkeletonBox';

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
      {range(children).map(number => <SkeletonBox key={number} />)}
    </ul>
  );
}
