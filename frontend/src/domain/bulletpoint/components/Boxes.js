// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../types';

type Props = {|
  +bulletpoints: Array<FetchedBulletpointType>,
  +children: (FetchedBulletpointType) => any
|};
const Boxes = ({ bulletpoints, children }: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => children(bulletpoint))}
  </ul>
);

export default Boxes;
