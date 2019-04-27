// @flow
import React from 'react';
import type { FetchedBulletpointType, PointType } from '../../../types';
import * as format from '../../../formats';
import Rating from './Rating';
import Options from './Options';
import Details from './Details';

type Props = {|
  +children: FetchedBulletpointType,
  +onRatingChange?: (point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: () => (void),
|};
const InnerContent = ({
  children,
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <>
    <Rating onRatingChange={onRatingChange}>{children}</Rating>
    {format.withComparisons(format.replaceMatches(children), children)}
    <Options
      onDeleteClick={onDeleteClick}
      onEditClick={onEditClick ? () => onEditClick(children.id) : undefined}
    />
    <br />
    <Details>{children}</Details>
  </>
);

export default InnerContent;
