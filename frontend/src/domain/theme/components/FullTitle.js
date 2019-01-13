// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import Reference from './Reference';
import AllTags from '../../tags/components/All';
import Star from '../../../components/Star';
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
  +onStarClick: (boolean) => (void),
|};
const FullTitle = ({ theme, onStarClick }: Props) => (
  <>
    <div>
      <Star active={theme.is_starred} onClick={onStarClick} />
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
    <div>
      <small>
        {theme.alternative_names.join(', ')}
      </small>
    </div>
    <AllTags tags={theme.tags} />
  </>
);

export default FullTitle;
