// @flow
import React, { useState } from 'react';
import styled from 'styled-components';
import className from 'classnames';
import { intersection, isEmpty } from 'lodash';
import moment from 'moment/moment';
import type { FetchedBulletpointType, PointType } from '../../../types';
import InnerContent from './InnerContent';
import type { FetchedUserTagType, FetchedUserType } from '../../../../user/types';
import { getAvatar } from '../../../../user';
import UserBadges from '../../../../tags/components/UserBadges';

const Date = styled.p`
  margin: 0;
`;

const Username = styled.p`
  margin: 0;
`;

const Separator = styled.hr`
  margin-top: 8px;
  margin-bottom: 0;
  border: 0;
  border-top: 1px solid #282828;
`;

const InfoButton = styled.span`
  float: right;
  cursor: pointer;
  position: absolute;
  bottom: 0;
  right: 0;
  top: initial;
  margin-bottom: 2px;
`;

const MoreButton = styled(InfoButton)`
  color: #7e7e7e;
`;

const LessButton = styled(InfoButton)`
  color: #ffffff;
`;

type MoreInfoProps = {|
  +bulletpoint: FetchedBulletpointType,
  +getUser: () => (FetchedUserType),
  +getTags: () => (Array<FetchedUserTagType>),
  +more: boolean,
|};
const MoreInfo = ({
  getUser,
  getTags,
  bulletpoint,
  more,
}: MoreInfoProps) => {
  if (!more) {
    return null;
  }
  const user = getUser();
  if (user === null || user === undefined) {
    return null;
  }
  return (
    <>
      <Separator />
      <div className="row">
        <div className="col-sm-2">
          <Date>{moment(bulletpoint.created_at).format('DD.MM.YYYY')}</Date>
          <div className="well well-sm" style={{ display: 'inline-block', marginBottom: 0 }}>
            <img src={getAvatar(user, 50, 50)} alt={user.username} className="img-rounded" />
            <Username>{user.username}</Username>
            {<UserBadges tags={getTags()} link={(id, slug) => `/themes/tag/${id}/${slug}`} />}
          </div>
        </div>
      </div>
    </>
  );
};

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +highlights: Array<number>,
  +onRatingChange?: (point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: () => (void),
  +getUser: () => (FetchedUserType),
  +getTags: () => (Array<FetchedUserTagType>),
  +onMoreClick: (() => void) => (void),
|};
export default function ({
  bulletpoint,
  highlights,
  onRatingChange,
  onEditClick,
  onDeleteClick,
  getUser,
  getTags,
  onMoreClick,
}: Props) {
  const [more, setMore] = useState(false);

  const isHighlighted = (
    id: Array<number>,
    referencedThemeId: Array<number>,
    comparedThemeId: Array<number>,
  ) => (
    !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
  );

  const showMore = (more: boolean) => {
    if (typeof onMoreClick !== 'undefined') {
      onMoreClick(() => setMore(more));
    }
  };

  return (
    <li
      style={{ height: more ? 205 : null, position: 'relative' }}
      className={className(
        'list-group-item',
        isHighlighted(
          highlights,
          bulletpoint.referenced_theme_id,
          bulletpoint.compared_theme_id,
        ) && 'active',
      )}
    >
      <InnerContent
        onRatingChange={onRatingChange}
        onEditClick={onEditClick}
        onDeleteClick={onDeleteClick}
      >
        {bulletpoint}
      </InnerContent>
      <MoreInfo
        getUser={getUser}
        getTags={getTags}
        more={more}
        bulletpoint={bulletpoint}
      />
      {more && (
        <LessButton
          title="méně"
          onClick={() => showMore(false)}
          className="glyphicon glyphicon-chevron-up"
          aria-hidden="true"
        />
      )}
      {!more && (
        <MoreButton
          title="více"
          onClick={() => showMore(true)}
          className="glyphicon glyphicon-chevron-down"
          aria-hidden="true"
        />
      )}
    </li>
  );
}
