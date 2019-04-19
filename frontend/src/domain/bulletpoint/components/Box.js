// @flow
import React from 'react';
import styled from 'styled-components';
import className from 'classnames';
import { intersection, isEmpty } from 'lodash';
import moment from 'moment';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';
import type { FetchedUserTagType, FetchedUserType } from '../../user/types';
import { getAvatar } from '../../user';
import UserLabels from '../../tags/components/UserLabels';

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

const GroupExpand = styled.span`
  font-size: 20px;
  margin: 9px;
  cursor: pointer;
`;

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +highlights?: Array<number>,
  +onRatingChange?: (point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: () => (void),
  +getUser?: () => (FetchedUserType),
  +getTags?: () => (Array<FetchedUserTagType>),
  +onExpandClick?: (number) => (void),
  +onMoreClick?: () => (void),
|};
type State = {|
  more: boolean,
  expand: boolean,
|};
export default class extends React.Component<Props, State> {
  state = {
    more: false,
    expand: false,
  };

  isHighlighted = (
    id: Array<number>,
    referencedThemeId: Array<number>,
    comparedThemeId: Array<number>,
  ) => (
    !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
  );

  showMore = (more: boolean) => {
    this.setState({ more }, () => {
      const { onMoreClick } = this.props;
      if (typeof onMoreClick !== 'undefined') {
        onMoreClick();
      }
    });
  };

  handleExpand = () => {
    const { onExpandClick, bulletpoint: { id } } = this.props;
    if (typeof onExpandClick !== 'undefined') {
      this.setState({ expand: true }, () => onExpandClick(id));
    }
  };

  render() {
    const {
      bulletpoint,
      highlights = [],
      onRatingChange,
      onEditClick,
      onDeleteClick,
      getUser,
      getTags,
    } = this.props;
    const { more, expand } = this.state;
    const user = more && typeof getUser !== 'undefined' ? getUser() : null;

    return (
      <>
        <li
          style={{ height: more ? 205 : 'initial', position: 'relative' }}
          className={className(
            'list-group-item',
            this.isHighlighted(
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
          {(more && user !== null) && (
            <>
              <Separator />
              <div className="row">
                <div className="col-sm-2">
                  <Date>{moment(bulletpoint.created_at).format('DD.MM.YYYY')}</Date>
                  <div className="well well-sm" style={{ display: 'inline-block', marginBottom: 0 }}>
                    <img src={getAvatar(user, 50, 50)} alt={user.username} className="img-rounded" />
                    <Username>{user.username}</Username>
                    {getTags && <UserLabels tags={getTags()} link={(id, slug) => `/themes/tag/${id}/${slug}`} />}
                  </div>
                </div>
              </div>
            </>
          )}
          {more && !isEmpty(user) && (
            <LessButton
              title="méně"
              onClick={() => this.showMore(false)}
              className="glyphicon glyphicon-chevron-up"
              aria-hidden="true"
            />
          )}
          {!more && !isEmpty(user) && (
            <MoreButton
              title="více"
              onClick={() => this.showMore(true)}
              className="glyphicon glyphicon-chevron-down"
              aria-hidden="true"
            />
          )}
        </li>
        {!expand && !isEmpty(bulletpoint.group.children_bulletpoints) && bulletpoint.group.children_bulletpoints.length !== 0 && <div className="text-center"><GroupExpand onClick={this.handleExpand} className="glyphicon glyphicon glyphicon-option-horizontal" aria-hidden="true" /></div>}
      </>
    );
  }
}
