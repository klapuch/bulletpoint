// @flow
import React from 'react';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import Reference from './Reference';
import AllTags from '../../tags/components/All';

const Separator = styled.span`
  padding-right: 10px;
`;

type Props = {|
  +theme: FetchedThemeType,
|};
const Detail = ({ theme }: Props) => (
  <>
    <Reference url={theme.reference.url} />
    <Separator />
    <AllTags tags={theme.tags} />
  </>
);

export default Detail;
