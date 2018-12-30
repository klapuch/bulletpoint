// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import * as bulletpoint from '../../domain/bulletpoint/endpoints';
import * as bulletpoints from '../../domain/bulletpoint/selects';
import * as contributedBulletpoint from '../../domain/contributed_bulletpoint/endpoints';
import * as contributedBulletpoints from '../../domain/contributed_bulletpoint/selects';
import * as themes from '../../domain/theme/selects';
import * as user from '../../domain/user';
import All from '../../domain/tags/components/All';
import Form from '../../domain/bulletpoint/components/Form';
import Loader from '../../ui/Loader';
import Reference from '../../domain/theme/components/Reference';
import SlugRedirect from '../../router/SlugRedirect';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../domain/bulletpoint/types';
import type { FetchedThemeType } from '../../domain/theme/types';
import type { FormTypes } from '../../domain/bulletpoint/components/Form';
import type { PointType } from '../../domain/bulletpoint_rating/types';
import { default as AllBulletpoints } from '../../domain/bulletpoint/components/All';
import { rate } from '../../domain/bulletpoint_rating/endpoints';
import { single } from '../../domain/theme/endpoints';

const Title = styled.h1`
  display: inline-block;
`;

const EditButton = styled.span`
  cursor: pointer;
  padding: 5px;
`;

type State = {|
  formType: FormTypes,
  bulletpointId: number | null,
  bulletpoint: ?PostedBulletpointType,
|};
type Props = {|
  +addBulletpoint: (theme: number, PostedBulletpointType, (void) => (void)) => (void),
  +editBulletpoint: (
    theme: number,
    bulletpointId: number,
    PostedBulletpointType,
    next: (void) => (void),
  ) => (void),
  +deleteBulletpoint: (
    theme: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => (void),
  +bulletpoints: Array<FetchedBulletpointType>,
  +contributedBulletpoints: Array<FetchedBulletpointType>,
  +changeRating: (theme: number, bulletpoint: number, point: PointType) => (void),
  +fetchBulletpoints: (number) => (void),
  +fetchContributedBulletpoints: (number) => (void),
  +fetchTheme: (number) => (void),
  +fetching: boolean,
  +getBulletpointById: (number) => FetchedBulletpointType,
  +match: Object,
  +theme: FetchedThemeType,
|};
const initState = {
  formType: 'default',
  bulletpointId: null,
  bulletpoint: null,
};
class Theme extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.reload();
  }

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const { match: { params: { id } } } = this.props;
    if (this.state.formType === 'add') {
      this.props.addBulletpoint(id, bulletpoint, this.reload);
    } else if (this.state.formType === 'edit' && this.state.bulletpointId !== null) {
      this.props.editBulletpoint(id, this.state.bulletpointId, bulletpoint, this.reload);
    }
  };

  reload = () => {
    this.setState(initState);
    const { match: { params: { id } } } = this.props;
    this.props.fetchTheme(id);
    this.props.fetchBulletpoints(id);
    this.props.fetchContributedBulletpoints(id);
  };

  handleRatingChange = (bulletpointId: number, point: PointType) => {
    const { match: { params: { id } } } = this.props;
    const bulletpoint = this.props.getBulletpointById(bulletpointId);
    this.props.changeRating(id, bulletpointId, bulletpoint.rating.user === point ? 0 : point);
  };

  handleDeleteClick = (bulletpointId: number) => {
    const { match: { params: { id } } } = this.props;
    this.props.deleteBulletpoint(id, bulletpointId, this.reload);
  };

  handleEditClick = (bulletpointId: number) => {
    const bulletpoint = this.props.getBulletpointById(bulletpointId);
    this.setState({
      formType: 'edit',
      bulletpointId,
      bulletpoint: {
        content: bulletpoint.content,
        source: bulletpoint.source,
      },
    });
  };

  handleAddClick = () => this.setState({ formType: 'add' });

  handleCancelClick = () => this.setState(initState);

  render() {
    const {
      theme,
      fetching,
      bulletpoints,
      contributedBulletpoints,
    } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={theme.name}>
        <Helmet><title>{theme.name}</title></Helmet>
        <div>
          <Title>{theme.name}</Title>
          <Reference url={theme.reference.url} />
          {
            user.isAdmin() && (
              <Link to={`/themes/${theme.id}/change`}>
                <EditButton className="glyphicon glyphicon-pencil" aria-hidden="true" />
              </Link>
            )
          }
        </div>
        <All tags={theme.tags} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpoints">Bulletpointy</h2>
            <AllBulletpoints
              bulletpoints={bulletpoints}
              onRatingChange={this.handleRatingChange}
              onEditClick={user.isAdmin() ? this.handleEditClick : undefined}
              onDeleteClick={user.isAdmin() ? this.handleDeleteClick : undefined}
            />
            {!isEmpty(contributedBulletpoints) && (
              <>
                <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
                <AllBulletpoints
                  bulletpoints={contributedBulletpoints}
                  onDeleteClick={this.handleDeleteClick}
                />
              </>
            )}
            {user.isLoggedIn() && (
              <Form
                bulletpoint={this.state.bulletpoint}
                onAddClick={this.handleAddClick}
                onCancelClick={this.handleCancelClick}
                type={this.state.formType}
                onSubmit={this.handleSubmit}
              />
            )}
          </div>
        </div>
        <br />
      </SlugRedirect>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id: theme } } }) => ({
  theme: themes.getById(theme, state),
  bulletpoints: bulletpoints.getByTheme(theme, state),
  contributedBulletpoints: contributedBulletpoints.getByTheme(theme, state),
  fetching: themes.singleFetching(theme, state)
    || bulletpoints.allFetching(theme, state)
    || contributedBulletpoints.allFetching(theme, state),
  getBulletpointById: (bulletpoint: number) => bulletpoints.getById(theme, bulletpoint, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (theme: number) => dispatch(single(theme)),
  deleteBulletpoint: (
    themeId: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => dispatch(
    user.isAdmin()
      ? bulletpoint.deleteOne(themeId, bulletpointId, next)
      : contributedBulletpoint.deleteOne(themeId, bulletpointId, next),
  ),
  addBulletpoint: (
    themeId: number,
    postedBulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(
    user.isAdmin()
      ? bulletpoint.add(themeId, postedBulletpoint, next)
      : contributedBulletpoint.add(themeId, postedBulletpoint, next),
  ),
  editBulletpoint: (
    themeId: number,
    bulletpointId: number,
    postedBulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(bulletpoint.edit(themeId, bulletpointId, postedBulletpoint, next)),
  fetchBulletpoints: (theme: number) => dispatch(bulletpoint.all(theme)),
  fetchContributedBulletpoints: (theme: number) => dispatch(contributedBulletpoint.all(theme)),
  changeRating: (
    themeId: number,
    bulletpointId: number,
    point: PointType,
  ) => rate(bulletpointId, point, () => dispatch(bulletpoint.updateSingle(themeId, bulletpointId))),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
