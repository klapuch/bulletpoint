// @flow
import React from 'react';
import type { FetchedTagType } from '../types';
import Label from './Label';

type Props = {|
  +tags: Array<FetchedTagType>,
|};
const Labels = ({ tags }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <Label id={tag.id} key={tag.id}>{tag.name}</Label>)
);

export default Labels;
