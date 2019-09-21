// @flow
import React from 'react';
import { DownButton, UpButton } from './RateButton';
import type { FetchedBulletpointType, PointType } from '../../../types';
import { UpPoint, DownPoint } from '../../../types';

type Props = {|
  +children: FetchedBulletpointType,
  +onRatingChange?: (point: PointType) => (void),
|};
const Rating = ({
  children,
  onRatingChange,
}: Props) => (
  <>
    <DownButton
      rated={children.rating.user === DownPoint}
      onClick={onRatingChange ? () => onRatingChange(DownPoint) : () => {}}
    >
      {children.rating.down}
    </DownButton>
    <UpButton
      rated={children.rating.user === UpPoint}
      onClick={onRatingChange ? () => onRatingChange(UpPoint) : () => {}}
    >
      {children.rating.up}
    </UpButton>
  </>
);

export default Rating;
