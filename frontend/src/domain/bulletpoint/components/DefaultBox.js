// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../types';
import InnerContributionContent from './InnerContributionContent';

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onDeleteClick: () => (void),
|};
export default function ({ bulletpoint, onDeleteClick }: Props) {
  return (
    <li className="list-group-item">
      <InnerContributionContent onDeleteClick={onDeleteClick}>
        {bulletpoint}
      </InnerContributionContent>
    </li>
  );
}
