// @flow
import React from 'react';
import styled from 'styled-components';
import { DownButton, UpButton } from '../theme/bulletpoint/components/RateButton';
import Source from '../theme/components/Source';
import * as session from '../access/session';
import type { FetchedBulletpointType, PointType } from '../theme/bulletpoint/types';

const EditButton = styled.span`
  cursor: pointer;
  float: right;
`;

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onRatingChange: (id: number, point: PointType) => (void),
  +onEditClick: (id: number) => (void),
|};
const Single = ({ bulletpoint, onRatingChange, onEditClick }: Props) => (
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
    {session.exists() && <EditButton className="glyphicon glyphicon-pencil" aria-hidden="true" onClick={() => onEditClick(bulletpoint.id)} />}
    <br />
    <small>
      <cite>
        <Source type={bulletpoint.source.type} link={bulletpoint.source.link} />
      </cite>
    </small>
  </>
);

export default Single;
