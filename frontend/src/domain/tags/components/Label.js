// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { Link } from 'react-router-dom';
import styled from 'styled-components';

const SpacyLabel = styled.span`
  margin-right: 7px;
`;

type Props = {|
  +children: string,
  +id: number,
  +link: (number, string) => string,
|};
const Label = ({ children, id, link }: Props) => (
  <Link className="no-link" to={link(id, getSlug(children))}>
    <SpacyLabel className="label label-default">{children}</SpacyLabel>
  </Link>
);

export default Label;
