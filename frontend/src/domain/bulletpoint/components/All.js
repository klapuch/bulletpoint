// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../types';
import type { PointType } from '../../bulletpoint_rating/types';
import Bulletpoint from './Single';

type Props = {|
  +bulletpoints: Array<FetchedBulletpointType>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const All = ({
  bulletpoints,
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
        <Bulletpoint
          onRatingChange={onRatingChange}
          onEditClick={onEditClick}
          onDeleteClick={onDeleteClick}
        >
          {bulletpoint}
        </Bulletpoint>
      </li>
    ))}
  </ul>
);

export default All;
