// @flow
import React from 'react';
import Source from './Source';
import type { FetchedBulletpointType } from '../../../types';

type Props = {|
  +children: FetchedBulletpointType,
|};
const Details = ({ children }: Props) => (
  <small>
    <cite>
      <Source>{children.source}</Source>
    </cite>
  </small>
);

export default Details;
