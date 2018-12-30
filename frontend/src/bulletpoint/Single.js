// @flow
import React from 'react';
import styled from 'styled-components';
import { DownButton, UpButton } from '../theme/bulletpoint/components/RateButton';
import Source from '../theme/components/Source';
import type { FetchedBulletpointType, PointType } from '../theme/bulletpoint/types';

const ActionButton = styled.span`
  cursor: pointer;
  float: right;
  padding-left: 5px;
`;

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (id: number) => (void),
  +onDeleteClick?: (id: number) => (void),
|};
const Single = ({ bulletpoint, onRatingChange, onEditClick, onDeleteClick }: Props) => (
  <>
    {onRatingChange && <DownButton
      rated={bulletpoint.rating.user === -1}
      onClick={() => onRatingChange(bulletpoint.id, -1)}
    >
      {bulletpoint.rating.down}
    </DownButton>}
    {onRatingChange && <UpButton
      rated={bulletpoint.rating.user === 1}
      onClick={() => onRatingChange(bulletpoint.id, 1)}
    >
      {bulletpoint.rating.up}
    </UpButton>}
    {bulletpoint.content}
    {onDeleteClick && <ActionButton className="text-danger glyphicon glyphicon-remove" aria-hidden="true" onClick={() => onDeleteClick(bulletpoint.id)} />}
    {onEditClick && <ActionButton className="glyphicon glyphicon-pencil" aria-hidden="true" onClick={() => onEditClick(bulletpoint.id)} />}
    <br />
    <small>
      <cite>
        <Source type={bulletpoint.source.type} link={bulletpoint.source.link} />
      </cite>
    </small>
  </>
);

export default Single;
