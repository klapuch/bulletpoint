// @flow
import React from 'react';
import type { FetchedBulletpointType, PointType } from '../types';
import * as format from '../formats';
import Rating from './Rating';
import Options from './Options';
import Details from './Details';

type Props = {|
  +children: FetchedBulletpointType,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (id: number) => (void),
  +onDeleteClick?: (id: number) => (void),
|};
const Single = ({
  children, onRatingChange, onEditClick, onDeleteClick,
}: Props) => (
  <>
    <Rating onRatingChange={onRatingChange}>{children}</Rating>
    {format.withComparisons(format.replaceMatches(children), children)}
    <Options onDeleteClick={onDeleteClick} onEditClick={onEditClick}>{children}</Options>
    <br />
    <Details>{children}</Details>
  </>
);

export default Single;
