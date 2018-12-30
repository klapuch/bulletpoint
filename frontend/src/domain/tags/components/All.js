// @flow
import React from 'react';
import type { TagType } from '../types';
import Tag from './Single';

type Props = {|
  +tags: Array<TagType>,
|};
const Tags = ({ tags }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <Tag id={tag.id} key={tag.id}>{tag.name}</Tag>)
);

export default Tags;
