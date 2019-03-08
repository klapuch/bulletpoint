// @flow
import React from 'react';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';

type Props = {|
  +bulletpoints: Array<FetchedBulletpointType>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const Boxes = ({
  bulletpoints,
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
        <InnerContent
          onRatingChange={onRatingChange}
          onEditClick={onEditClick}
          onDeleteClick={onDeleteClick}
        >
          {bulletpoint}
        </InnerContent>
      </li>
    ))}
  </ul>
);

export default Boxes;
