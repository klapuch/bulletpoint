// @flow
import React from 'react';
import styled from 'styled-components';

const RateButton = styled.span`
  cursor: pointer;
`;

type Props = {|
  +children: number,
  +onClick: (void) => (void),
  +rated: boolean,
|};

export const UpButton = ({ children, onClick, rated }: Props) => (
  <RateButton className={['badge', 'alert-success', 'badge-guest', rated ? '' : 'opposite-rating'].join(' ')} onClick={onClick}>
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);

export const DownButton = ({ children, onClick, rated }: Props) => (
  <RateButton className={['badge', 'alert-danger', 'badge-guest', rated ? '' : 'opposite-rating'].join(' ')} onClick={onClick}>
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);
