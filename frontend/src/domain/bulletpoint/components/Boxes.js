// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../types';

type Props = {|
  +highlights?: Array<number>,
  +bulletpoints: Array<FetchedBulletpointType>,
  +onEditClick?: (number) => (void),
  +onExpandClick?: (number) => (void),
  +onDeleteClick?: () => (void),
  +box: Object,
|};
const Boxes = ({
  box: Box,
  highlights = [],
  bulletpoints,
  onExpandClick,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <Box
        onDeleteClick={onDeleteClick}
        onEditClick={onEditClick}
        bulletpoint={bulletpoint}
        key={`bulletpoint-${bulletpoint.id}`}
        onExpandClick={onExpandClick}
        highlights={highlights}
      />
    ))}
  </ul>
);

export default Boxes;
