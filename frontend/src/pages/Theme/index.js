// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import SlugRedirect from '../../router/SlugRedirect';
import { single } from '../../theme/endpoints';
import { all as allBulletpoints, add, edit } from '../../theme/bulletpoint/endpoints';
import { all as allContributedBulletpoints, add as contributeBulletpoint } from '../../theme/contributed_bulletpoint/endpoints';
import { rate } from '../../theme/bulletpoint/rating/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';
import * as user from '../../user';
import {
  allFetching as fetchingAllThemeBulletpoints,
  getByTheme as getThemeBulletpoints,
  getById as getBulletpointById,
} from '../../theme/bulletpoint/selects';
import {
  allFetching as fetchingAllThemeContributedBulletpoints,
  getByTheme as getThemeContributedBulletpoints,
  getById as getContributedBulletpointById,
} from '../../theme/contributed_bulletpoint/selects';
import Loader from '../../ui/Loader';
import Form from '../../bulletpoint/Form';
import Tags from '../../theme/components/Tags';
import Reference from '../../theme/components/Reference';
import { default as AllBulletpoints } from '../../bulletpoint/All';
import type { FetchedThemeType } from '../../theme/types';
import type { FetchedBulletpointType, PointType, PostedBulletpointType } from '../../theme/bulletpoint/types';
import type { FormTypes } from '../../bulletpoint/Form';

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
    (void) => (void),
  ) => (void),
  +bulletpoints: Array<FetchedBulletpointType>,
  +changeRating: (theme: number, bulletpoint: number, point: PointType) => (void),
  +fetchBulletpoints: (number) => (void),
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
    const { theme, fetching, bulletpoints, contributedBulletpoints } = this.props;
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
        <Tags tags={theme.tags} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpoints">Bulletpointy</h2>
            <AllBulletpoints
              bulletpoints={bulletpoints}
              onRatingChange={this.handleRatingChange}
              onEditClick={this.handleEditClick}
            />
            {!isEmpty(contributedBulletpoints) && (
              <>
                <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
                <AllBulletpoints bulletpoints={contributedBulletpoints} />
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
  theme: getById(theme, state),
  bulletpoints: getThemeBulletpoints(theme, state),
  contributedBulletpoints: getThemeContributedBulletpoints(theme, state),
  fetching: themeFetching(theme, state)
    || fetchingAllThemeBulletpoints(theme, state)
    || fetchingAllThemeContributedBulletpoints(theme, state),
  getBulletpointById: (bulletpoint: number) => getBulletpointById(theme, bulletpoint, state),
  getContributedBulletpointById: (bulletpoint: number) => getContributedBulletpointById(theme, bulletpoint, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (theme: number) => dispatch(single(theme)),
  addBulletpoint: (
    theme: number,
    bulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(user.isAdmin() ? add(theme, bulletpoint, next) : contributeBulletpoint(theme, bulletpoint, next)),
  editBulletpoint: (
    theme: number,
    bulletpointId: number,
    bulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(edit(theme, bulletpointId, bulletpoint, next)),
  fetchBulletpoints: (theme: number) => dispatch(allBulletpoints(theme)),
  fetchContributedBulletpoints: (theme: number) => dispatch(allContributedBulletpoints(theme)),
  changeRating: (
    theme: number,
    bulletpoint: number,
    point: PointType,
  ) => dispatch(rate(theme, bulletpoint, point)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
