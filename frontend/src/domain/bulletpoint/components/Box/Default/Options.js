// @flow
import React from 'react';
import styled from 'styled-components';

const ActionButton = styled.span`
  cursor: pointer;
  float: right;
  padding-left: 5px;
`;

type Props = {|
  +onEditClick?: () => (void),
  +onDeleteClick?: () => (void),
|};
const Options = ({
  onEditClick,
  onDeleteClick,
}: Props) => (
  <>
    {onDeleteClick && <ActionButton className="text-danger glyphicon glyphicon-remove" aria-hidden="true" onClick={onDeleteClick} />}
    {onEditClick && <ActionButton className="glyphicon glyphicon-pencil" aria-hidden="true" onClick={onEditClick} />}
  </>
);

export default Options;
