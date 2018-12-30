// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { Link } from 'react-router-dom';

type Props = {|
  +children: string,
  +id: number,
|};
const Tag = ({ children, id }: Props) => (
  <Link className="no-link" to={`/themes/tag/${id}/${getSlug(children)}`}>
    <span style={{ marginRight: 7 }} className="label label-default">{children}</span>
  </Link>
);

export default Tag;
