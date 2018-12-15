// @flow
import React from 'react';
import styled from 'styled-components';

const RateButton = styled.span`
  cursor: pointer;
`;

type Props = {|
  +children: number,
  +onClick: (void) => (void),
|};

export const UpButton = ({ children, onClick }: Props) => (
  <RateButton className="badge alert-success badge-guest" onClick={onClick}>
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);

export const DownButton = ({ children, onClick }: Props) => (
  <RateButton className="badge alert-danger badge-guest" onClick={onClick}>
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);
