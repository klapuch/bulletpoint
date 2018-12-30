// @flow
import React from 'react';
import type { FetchedBulletpointType, PointType } from '../theme/bulletpoint/types';
import Single from './Single';

type Props = {|
  +bulletpoints: Array<FetchedBulletpointType>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const All = ({ bulletpoints, onRatingChange, onEditClick, onDeleteClick }: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
        <Single
          bulletpoint={bulletpoint}
          onRatingChange={onRatingChange}
          onEditClick={onEditClick}
          onDeleteClick={onDeleteClick}
        />
      </li>
    ))}
  </ul>
);

export default All;
