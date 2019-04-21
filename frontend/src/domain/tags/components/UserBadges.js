// @flow
import React from 'react';
import UserBadge from './UserBadge';
import type { FetchedUserTagType } from '../../user/types';

type Props = {|
  +tags: Array<FetchedUserTagType>,
  +link: (number, string) => string,
|};
const UserBadges = ({ tags, link }: Props) => (
  // $FlowFixMe Not sure why
  tags.map(tag => <UserBadge id={tag.tag_id} link={link} key={tag.tag_id}>{tag}</UserBadge>)
);

export default UserBadges;
