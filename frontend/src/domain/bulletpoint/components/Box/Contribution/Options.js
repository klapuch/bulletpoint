// @flow
import React from 'react';
import styled from 'styled-components';

const ActionButton = styled.span`
  cursor: pointer;
  float: right;
  padding-left: 5px;
`;

type Props = {|
  +onDeleteClick: () => (void),
|};
const Options = ({ onDeleteClick }: Props) => (
  <>
    {onDeleteClick && <ActionButton className="text-danger glyphicon glyphicon-remove" aria-hidden="true" onClick={onDeleteClick} />}
  </>
);

export default Options;
