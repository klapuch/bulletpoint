// @flow
import React from 'react';
import { isEmpty } from 'lodash';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import Options from './Options';

const Title = styled.h1`
  display: inline-block;
`;

type Props = {|
  +theme: FetchedThemeType,
|};
const Titles = ({ theme }: Props) => (
  <>
    <Title>{theme.name}</Title>
    <Options theme={theme} />
    {!isEmpty(theme.alternative_names) && (
      <small>
        {`(${theme.alternative_names.join(', ')})`}
      </small>
    )}
  </>
);

export default Titles;
