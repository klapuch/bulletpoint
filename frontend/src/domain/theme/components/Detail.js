// @flow
import React from 'react';
import styled from 'styled-components';
import type { FetchedThemeType } from '../types';
import Reference from './Reference';
import Labels from '../../tags/components/Labels';

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
    <Labels tags={theme.tags} link={(id, slug) => `/themes/tag/${id}/${slug}`} />
  </>
);

export default Detail;
