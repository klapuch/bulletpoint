// @flow
import React from 'react';
import { range } from 'lodash';
import SkeletonPreview from './SkeletonPreview';

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
          <SkeletonPreview />
          <hr />
        </React.Fragment>
      ))}
    </>
  );
}
