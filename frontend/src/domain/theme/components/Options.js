// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import * as user from '../../user';

const EditButton = styled.span`
  cursor: pointer;
  padding: 5px;
`;

type Props = {|
  +theme: FetchedThemeType,
|};
const Options = ({ theme }: Props) => (
  <>
    {
      user.isAdmin() && (
        <Link to={`/themes/${theme.id}/change`}>
          <EditButton className="glyphicon glyphicon-pencil" aria-hidden="true" />
        </Link>
      )
    }
  </>
);

export default Options;
