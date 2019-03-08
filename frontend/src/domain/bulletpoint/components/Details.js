// @flow
import React from 'react';
import Source from '../../theme/components/Source';
import type { FetchedBulletpointType } from '../types';

type Props = {|
  +children: FetchedBulletpointType,
|};
const Details = ({
  children,
}: Props) => (
  <small>
    <cite>
      <Source type={children.source.type} link={children.source.link} />
    </cite>
  </small>
);

export default Details;
