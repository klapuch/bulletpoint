// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../../../types';
import InnerContent from '../Contribution/InnerContent';

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onDeleteClick: () => (void),
|};
export default function ({ bulletpoint, onDeleteClick }: Props) {
  return (
    <li className="list-group-item">
      <InnerContent onDeleteClick={onDeleteClick}>
        {bulletpoint}
      </InnerContent>
    </li>
  );
}
