// @flow
import React from 'react';
import { DownButton, UpButton } from './RateButton';
import type { FetchedBulletpointType, PointType } from '../types';

type Props = {|
  +children: FetchedBulletpointType,
  +onRatingChange?: (point: PointType) => (void),
|};
const Rating = ({
  children,
  onRatingChange,
}: Props) => (
  <>
    {onRatingChange && (
    <DownButton
      rated={children.rating.user === -1}
      onClick={onRatingChange ? () => onRatingChange(-1) : () => {}}
    >
      {children.rating.down}
    </DownButton>
    )}
    {onRatingChange && (
    <UpButton
      rated={children.rating.user === 1}
      onClick={onRatingChange ? () => onRatingChange(1) : () => {}}
    >
      {children.rating.up}
    </UpButton>
    )}
  </>
);

export default Rating;
