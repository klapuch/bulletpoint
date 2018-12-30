// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import Reference from './Reference';
import AllTags from '../../tags/components/All';
import * as user from '../../user';

const Title = styled.h1`
  display: inline-block;
`;

const EditButton = styled.span`
  cursor: pointer;
  padding: 5px;
`;

type Props = {|
  +theme: FetchedThemeType,
|};
const FullTitle = ({ theme }: Props) => (
  <>
    <div>
      <Title>{theme.name}</Title>
      <Reference url={theme.reference.url} />
      {
        user.isAdmin() && (
          <Link to={`/themes/${theme.id}/change`}>
            <EditButton className="glyphicon glyphicon-pencil" aria-hidden="true" />
          </Link>
        )
      }
    </div>
    <AllTags tags={theme.tags} />
  </>
);

export default FullTitle;
