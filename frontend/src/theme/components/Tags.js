// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import type { TagType } from '../../tags/types';

type TagProps = {|
  +children: string,
  +id: number,
|};
const Tag = ({ children, id }: TagProps) => (
  <Link className="no-link" to={`/themes/tag/${id}`}>
    <span style={{ marginRight: 7 }} className="label label-default">{children}</span>
  </Link>
);

type TagsProps = {|
  +tags: Array<TagType>,
|};
const Tags = ({ tags }: TagsProps) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <Tag id={tag.id} key={tag.id}>{tag.name}</Tag>)
);

export default Tags;
