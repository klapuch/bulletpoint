// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import type { FetchedUserTagType } from '../../user/types';

const rankText = (rank: number) => {
  switch (rank) {
    case 1: return '1st';
    case 2: return '2nd';
    case 3: return '3rd';
    default: return `${rank}th`;
  }
};

const SpacyLabel = styled.span`
  margin-right: 7px;
`;

const RankReputation = styled.small`
  padding-left: 3px;
  color: #d2d2d2;
  font-size: 90%;
`;

type Props = {|
  +children: FetchedUserTagType,
  +id: number,
  +link: (number, string) => string,
|};
const UserBadge = ({ children: tag, id, link }: Props) => (
  <Link className="no-link" to={link(id, getSlug(tag.name))}>
    <SpacyLabel className="label label-default">
      {tag.name}
      <RankReputation>
        {rankText(tag.rank)} ({tag.reputation.toLocaleString()} rep.)
      </RankReputation>
    </SpacyLabel>
  </Link>
);

export default UserBadge;
