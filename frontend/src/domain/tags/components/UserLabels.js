// @flow
import React from 'react';
import UserLabel from './UserLabel';
import type { FetchedUserTagType } from '../../user/types';

type Props = {|
  +tags: Array<FetchedUserTagType>,
  +link: (number, string) => string,
|};
const UserLabels = ({ tags, link }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <UserLabel id={tag.tag_id} link={link} key={tag.tag_id}>{tag}</UserLabel>)
);

export default UserLabels;
