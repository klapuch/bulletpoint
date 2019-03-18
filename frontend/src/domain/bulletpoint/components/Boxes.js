// @flow
import React from 'react';
import Box from './Box';
import type { FetchedBulletpointType, PointType } from '../types';

type Props = {|
  +highlights?: Array<number>,
  +bulletpoints: Array<FetchedBulletpointType>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const Boxes = ({
  highlights = [],
  bulletpoints,
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <Box
        bulletpoint={bulletpoint}
        key={`bulletpoint-${bulletpoint.id}`}
        onRatingChange={onRatingChange}
        onEditClick={onEditClick}
        onDeleteClick={onDeleteClick}
        highlights={highlights}
      />
    ))}
  </ul>
);

export default Boxes;
