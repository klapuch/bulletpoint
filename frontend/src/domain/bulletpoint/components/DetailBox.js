// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType, PointType } from '../types';
import Box from './Box';
import * as users from '../../user/selects';
import * as user from '../../user/endpoints';
import * as me from '../../user';
import * as themes from '../../theme/selects';
import type { FetchedUserTagType, FetchedUserType } from '../../user/types';
import type { FetchedTagType } from '../../tags/types';
import * as bulletpoint from '../endpoints';

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +highlights?: Array<number>,
  +onEditClick?: (number) => (void),
  +onDeleteClick?: () => (void),
  +fetchUser: () => (void),
  +fetchTags: (Array<FetchedTagType>) => (void),
  +getUser: () => (FetchedUserType),
  +getThemeTags: () => (Array<FetchedTagType>),
  +changeRating: (PointType) => (void),
  +getTags: () => (Array<FetchedUserTagType>),
  +onExpandClick?: (number) => (void),
  +deleteOne: (next?: (void) => (void)) => (void),
|};
type State = {|
  more: boolean,
  expand: boolean,
|};
class DetailBox extends React.Component<Props, State> {
  handleMoreClick = () => Promise.resolve()
    .then(this.props.fetchUser)
    .then(() => this.props.fetchTags(this.props.getThemeTags()));

  handleDeleteClick = () => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      this.props.deleteOne(this.props.onDeleteClick);
    }
  };

  handleRatingChange = (point: PointType) => {
    const { bulletpoint } = this.props;
    this.props.changeRating(bulletpoint.rating.user === point ? 0 : point);
  };

  render() {
    return (
      <Box
        bulletpoint={this.props.bulletpoint}
        highlights={this.props.highlights || []}
        onDeleteClick={me.isAdmin() ? this.handleDeleteClick : undefined}
        onEditClick={me.isAdmin() ? this.props.onEditClick : undefined}
        onExpandClick={this.props.onExpandClick}
        onMoreClick={this.handleMoreClick}
        onRatingChange={me.isLoggedIn() ? this.handleRatingChange : undefined}
        getTags={this.props.getTags}
        getUser={this.props.getUser}
      />
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
});
const mapDispatchToProps = (dispatch, { bulletpoint: { id, user_id, theme_id } }) => ({
  fetchUser: () => dispatch(user.fetchSingle(user_id)),
  fetchTags: (
    tags: Array<FetchedTagType>,
  ) => dispatch(user.fetchTags(user_id, tags.map(tag => tag.id))),
  deleteOne: (
    next: (void) => (void),
  ) => dispatch(bulletpoint.deleteOne(theme_id, id, next)),
  changeRating: (point: PointType) => bulletpoint.rate(
    id,
    point,
    () => dispatch(bulletpoint.updateSingle(theme_id, id)),
  ),
});
export default connect(mapStateToProps, mapDispatchToProps)(DetailBox);
