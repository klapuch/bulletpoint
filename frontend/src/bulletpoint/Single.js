// @flow
import React from 'react';
import { DownButton, UpButton } from '../theme/bulletpoint/components/RateButton';
import Source from '../theme/components/Source';
import type { FetchedBulletpointType, PointType } from '../theme/bulletpoint/types';

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onRatingChange: (id: number, point: PointType) => (void),
|};
const Single = ({ bulletpoint, onRatingChange }: Props) => (
  <>
    <DownButton
      rated={bulletpoint.rating.user === -1}
      onClick={() => onRatingChange(bulletpoint.id, -1)}
    >
      {bulletpoint.rating.down}
    </DownButton>
    <UpButton
      rated={bulletpoint.rating.user === 1}
      onClick={() => onRatingChange(bulletpoint.id, 1)}
    >
      {bulletpoint.rating.up}
    </UpButton>
    {bulletpoint.content}
    <br />
    <small>
      <cite>
        <Source type={bulletpoint.source.type} link={bulletpoint.source.link} />
      </cite>
    </small>
  </>
);

export default Single;
