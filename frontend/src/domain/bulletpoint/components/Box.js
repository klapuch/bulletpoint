// @flow
import React from 'react';
import styled from 'styled-components';
import className from 'classnames';
import {connect} from "react-redux";
import { intersection, isEmpty } from 'lodash';
import moment from 'moment';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';
import * as users from "../../user/selects";
import * as user from "../../user/endpoints";

const Separator = styled.hr`
  margin-top: 8px;
  margin-bottom: 0;
  border: 0;
  border-top: 1px solid #282828;
`;

const MoreButton = styled.span`
  float: right;
  margin-right: -14px;
  margin-top: 14px;
  color: #7e7e7e;
  cursor: pointer;
`;

const LessButton = styled.span`
  float: right;
  margin-right: -14px;
  margin-top: 14px;
  color: #ffffff;
  cursor: pointer;
`;

const isHighlighted = (
  id: Array<number>,
  referencedThemeId: Array<number>,
  comparedThemeId: Array<number>,
) => (
  !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
);


type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +highlights?: Array<number>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
type State = {|
  more: boolean,
|};
class Box extends React.Component<Props, State> {
  state = {
    more: false,
  };

  componentDidUpdate(prevProps: Props, prevState: State) {
    if (this.state.more && this.state.more !== prevState.more) {
      this.props.fetchUser(this.props.bulletpoint.user_id);
    }
  }

  showMore = (more: boolean) => {
    this.setState({ more });
  };

  render() {
    const {
      bulletpoint,
      highlights = [],
      onRatingChange,
      onEditClick,
      onDeleteClick,
    } = this.props;
    const { more } = this.state;
    const { bulletpoint: { user_id: bulletpointUserId } } = this.props;
    const userInfo = this.props.getUser(bulletpointUserId);
    if (more && (this.props.isFetching(bulletpointUserId) || isEmpty(userInfo))) {
      return null;
    }

    return (
      <li
        style={{ height: more ? 300 : 'initial' }}
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
        {(more && !isEmpty(userInfo)) && (
          <>
            <Separator />
            <div className="row">
              <div className="col-sm-2">
                <small>{moment(bulletpoint.created_at).format('DD.MM.YYYY')}</small>
                <img src={users.getAvatar(userInfo, 50, 50)} />
                <p>{userInfo.username}</p>
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
    );
  }
}

const mapStateToProps = (state) => ({
  getUser: (id: number) => users.getById(id, state),
  isFetching: (id: number) => users.isFetching(id, state),
});
const mapDispatchToProps = dispatch => ({
  fetchUser: (id: number) => dispatch(user.fetchSingle(id)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Box);
