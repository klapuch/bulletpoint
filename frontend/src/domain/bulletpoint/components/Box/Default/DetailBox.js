// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType, PointType } from '../../../types';
import RichBox from './RichBox';
import * as users from '../../../../user/selects';
import * as user from '../../../../user/actions';
import * as me from '../../../../user';
import * as themes from '../../../../theme/selects';
import type { FetchedUserTagType, FetchedUserType } from '../../../../user/types';
import type { FetchedTagType } from '../../../../tags/types';
import * as bulletpoint from '../../../actions';
import * as message from '../../../../../ui/message/actions';

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
  +deleteSingle: () => (void),
  +receivedError: (string),
|};
type State = {|
  more: boolean,
|};
class DetailBox extends React.Component<Props, State> {
  handleMoreClick = () => Promise.resolve()
    .then(() => this.props.fetchUser())
    .then(() => this.props.fetchTags(this.props.getThemeTags()))
    // $FlowFixMe correct string from endpoint.js
    .catch(this.props.receivedError);

  handleDeleteClick = () => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      this.props.deleteSingle(this.props.onDeleteClick);
    }
  };

  handleRatingChange = (point: PointType) => {
    const { bulletpoint } = this.props;
    this.props.changeRating(bulletpoint.rating.user === point ? 0 : point);
  };

  render() {
    return (
      <RichBox
        bulletpoint={this.props.bulletpoint}
        highlights={this.props.highlights || []}
        onDeleteClick={me.isAdmin() ? this.handleDeleteClick : undefined}
        onEditClick={me.isAdmin() ? this.props.onEditClick : undefined}
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
    themes.getTagIds(themes.getById(theme_id, state)),
    state,
  ),
  getThemeTags: () => themes.getById(theme_id, state).tags,
});
const mapDispatchToProps = (dispatch, { bulletpoint: { id, user_id, theme_id } }) => ({
  receivedError: error => dispatch(message.receivedError(error)),
  fetchUser: () => dispatch(user.fetchSingle(user_id)),
  fetchTags: (
    tags: Array<FetchedTagType>,
  ) => dispatch(user.fetchTags(user_id, tags.map(tag => tag.id))),
  deleteSingle: (next) => dispatch(bulletpoint.deleteSingle(theme_id, id, next)),
  changeRating: (point: PointType) => dispatch(bulletpoint.rate(id, theme_id, point)),
});
export default connect(mapStateToProps, mapDispatchToProps)(DetailBox);
