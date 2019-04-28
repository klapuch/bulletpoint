// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import styled from 'styled-components';

const SpacyLabel = styled.span`
  margin-right: 7px;
`;

type Props = {|
  +children: string,
|};
const FakeLabel = ({ children }: Props) => (
  <Link className="no-link" to="/">
    <SpacyLabel style={{ color: '#424242' }} className="label label-default">
      {children}
    </SpacyLabel>
  </Link>
);

export default FakeLabel;
