// @flow
import React from 'react';
import styled from 'styled-components';
import type { FetchedBulletpointType } from '../types';

const ActionButton = styled.span`
  cursor: pointer;
  float: right;
  padding-left: 5px;
`;

type Props = {|
  +children: FetchedBulletpointType,
  +onEditClick?: (id: number) => (void),
  +onDeleteClick?: (id: number) => (void),
|};
const Options = ({
  children, onEditClick, onDeleteClick,
}: Props) => (
  <>
    {onDeleteClick && <ActionButton className="text-danger glyphicon glyphicon-remove" aria-hidden="true" onClick={() => onDeleteClick(children.id)} />}
    {onEditClick && <ActionButton className="glyphicon glyphicon-pencil" aria-hidden="true" onClick={() => onEditClick(children.id)} />}
  </>
);

export default Options;
