// @flow
import React from 'react';
import type { FetchedTagType } from '../types';
import Tag from './Single';

type Props = {|
  +tags: Array<FetchedTagType>,
|};
const Tags = ({ tags }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <Tag id={tag.id} key={tag.id}>{tag.name}</Tag>)
);

export default Tags;
