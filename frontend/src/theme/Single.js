// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import Tags from './components/Tags';
import type { TagType } from '../tags/types';

type Props = {|
  +id: number,
  +children: string,
  +tags: Array<TagType>,
|};
const Single = ({ id, children, tags }: Props) => (
  <>
    <Link className="no-link" to={`/themes/${id}/${getSlug(children)}`}>
      <h2>{children}</h2>
    </Link>
    <Tags tags={tags} />
  </>
);

export default Single;
