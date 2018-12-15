// @flow
import React from 'react';

type TagProps = {|
  children: string,
|};
const Tag = ({ children }: TagProps) => <span style={{ marginRight: 7 }} className="label label-default">{children}</span>;

type TagsProps = {|
  texts: Array<string>,
|};
// $FlowFixMe Not sure why
const Tags = ({ texts }: TagsProps) => texts.map(text => <Tag key={text}>{text}</Tag>);

export default Tags;
