// @flow
import React from 'react';
import styled from 'styled-components';
import { DownButton, UpButton } from './RateButton';
import Source from '../../theme/components/Source';
import type { FetchedBulletpointType, PointType } from '../types';
import * as format from '../formats';

const ActionButton = styled.span`
  cursor: pointer;
  float: right;
  padding-left: 5px;
`;

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
    {onRatingChange && (
    <DownButton
      rated={children.rating.user === -1}
      onClick={() => onRatingChange(children.id, -1)}
    >
      {children.rating.down}
    </DownButton>
    )}
    {onRatingChange && (
    <UpButton
      rated={children.rating.user === 1}
      onClick={() => onRatingChange(children.id, 1)}
    >
      {children.rating.up}
    </UpButton>
    )}
    {format.replaceBulletpointMatches(children)}
    {onDeleteClick && <ActionButton className="text-danger glyphicon glyphicon-remove" aria-hidden="true" onClick={() => onDeleteClick(children.id)} />}
    {onEditClick && <ActionButton className="glyphicon glyphicon-pencil" aria-hidden="true" onClick={() => onEditClick(children.id)} />}
    <br />
    <small>
      <cite>
        <Source type={children.source.type} link={children.source.link} />
      </cite>
    </small>
  </>
);

export default Single;
