// @flow
import React from 'react';
import styled from 'styled-components';
import className from 'classnames';
import { connect } from 'react-redux';
import { intersection, isEmpty } from 'lodash';
import moment from 'moment';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';
import * as users from '../../user/selects';
import * as user from '../../user/endpoints';
import * as themes from '../../theme/selects';
import type { FetchedUserTagType, FetchedUserType } from '../../user/types';
import { getAvatar } from '../../user';
import type { FetchedTagType } from '../../tags/types';
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
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
  +fetchUser: () => (void),
  +fetchTags: (Array<FetchedTagType>) => (void),
  +getUser: () => (FetchedUserType),
  +getThemeTags: () => (Array<FetchedTagType>),
  +getTags: () => (Array<FetchedUserTagType>),
  +isFetching: () => boolean,
  +onExpand: (number) => (void),
|};
type State = {|
  more: boolean,
  expand: boolean,
|};
class Box extends React.Component<Props, State> {
  state = {
    more: false,
    expand: false,
  };

  componentDidUpdate(prevProps: Props, prevState: State) {
    const { more } = this.state;
    if (more && more !== prevState.more) {
      this.props.fetchUser();
      this.props.fetchTags(this.props.getThemeTags());
    }
  }

  isHighlighted = (
    id: Array<number>,
    referencedThemeId: Array<number>,
    comparedThemeId: Array<number>,
  ) => (
    !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
  );

  showMore = (more: boolean) => {
    this.setState({ more });
  };

  expand = () => {
    this.setState({ expand: true });
    this.props.onExpand(this.props.bulletpoint.id);
  };

  render() {
    const {
      bulletpoint,
      highlights = [],
      onRatingChange,
      onEditClick,
      onDeleteClick,
    } = this.props;
    const { more, expand } = this.state;
    if (more && this.props.isFetching()) {
      return null;
    }

    const userInfo = this.props.getUser();

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
          {(more && !isEmpty(userInfo)) && (
            <>
              <Separator />
              <div className="row">
                <div className="col-sm-2">
                  <Date>{moment(bulletpoint.created_at).format('DD.MM.YYYY')}</Date>
                  <div className="well well-sm" style={{ display: 'inline-block', marginBottom: 0 }}>
                    <img src={getAvatar(userInfo, 50, 50)} alt={userInfo.username} className="img-rounded" />
                    <Username>{userInfo.username}</Username>
                    <UserLabels tags={this.props.getTags()} link={(id, slug) => `/themes/tag/${id}/${slug}`} />
                  </div>
                </div>
              </div>
            </>
          )}
          {more
            ? (
              <LessButton
                title="méně"
                onClick={() => this.showMore(false)}
                className="glyphicon glyphicon-chevron-up"
                aria-hidden="true"
              />
            )
            : (
              <MoreButton
                title="více"
                onClick={() => this.showMore(true)}
                className="glyphicon glyphicon-chevron-down"
                aria-hidden="true"
              />
            )
          }
        </li>
        {!expand && bulletpoint.group.children_bulletpoints.length !== 0 && <div className="text-center"><GroupExpand onClick={this.expand} className="glyphicon glyphicon glyphicon-option-horizontal" aria-hidden="true" /></div>}
      </>
    );
  }
}

const mapStateToProps = (state, { bulletpoint: { user_id, theme_id } }) => ({
  getUser: () => users.getById(user_id, state),
  getTags: () => users.getSelectedTags(
    user_id,
    themes.getById(theme_id, state).tags.map(tag => tag.id),
    state,
  ),
  getThemeTags: () => themes.getById(theme_id, state).tags,
  isFetching: () => users.isFetching(user_id, state) || users.isFetchingTags(user_id, state),
});
const mapDispatchToProps = (dispatch, { bulletpoint: { user_id } }) => ({
  fetchUser: () => dispatch(user.fetchSingle(user_id)),
  fetchTags: (
    tags: Array<FetchedTagType>,
  ) => dispatch(user.fetchTags(user_id, tags.map(tag => tag.id))),
});
export default connect(mapStateToProps, mapDispatchToProps)(Box);
