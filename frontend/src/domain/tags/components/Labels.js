// @flow
import React from 'react';
import type { FetchedTagType } from '../types';
import Label from './Label';

type Props = {|
  +tags: Array<FetchedTagType>,
  +link: (number, string) => string,
|};
const Labels = ({ tags, link }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <Label id={tag.id} link={link} key={tag.id}>{tag.name}</Label>)
);

export default Labels;
