// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import * as bulletpoint from '../../domain/bulletpoint/endpoints';
import * as bulletpoints from '../../domain/bulletpoint/selects';
import * as contributedBulletpoint from '../../domain/contributed_bulletpoint/endpoints';
import * as contributedBulletpoints from '../../domain/contributed_bulletpoint/selects';
import * as themes from '../../domain/theme/selects';
import * as user from '../../domain/user';
import Form from '../../domain/bulletpoint/components/Form';
import Loader from '../../ui/Loader';
import SlugRedirect from '../../router/SlugRedirect';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../domain/bulletpoint/types';
import type { FetchedThemeType } from '../../domain/theme/types';
import type { FormTypes } from '../../domain/bulletpoint/components/Form';
import type { PointType } from '../../domain/bulletpoint_rating/types';
import { default as AllBulletpoints } from '../../domain/bulletpoint/components/All';
import { rate } from '../../domain/bulletpoint_rating/endpoints';
import { single } from '../../domain/theme/endpoints';
import FullTitle from '../../domain/theme/components/FullTitle';

type State = {|
  formType: FormTypes,
  bulletpointId: number | null,
  bulletpoint: ?PostedBulletpointType,
|};
type Props = {|
  +addBulletpoint: (theme: number, PostedBulletpointType, (void) => (void)) => (Promise<any>),
  +editBulletpoint: (
    theme: number,
    bulletpointId: number,
    PostedBulletpointType,
    next: (void) => (void),
  ) => (Promise<any>),
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

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { id } } } = this.props;
    if (prevProps.match.params.id !== id) {
      this.reload();
    }
  }

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const { match: { params: { id } } } = this.props;
    if (this.state.formType === 'add') {
      return this.props.addBulletpoint(id, bulletpoint, this.reload);
    } else if (this.state.formType === 'edit' && this.state.bulletpointId !== null) {
      return this.props.editBulletpoint(id, this.state.bulletpointId, bulletpoint, this.reload);
    }
    return Promise.resolve();
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
        referenced_theme_id: bulletpoint.referenced_theme_id,
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
        <FullTitle theme={theme} />
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

const mapStateToProps = (state, { match: { params: { id: themeId } } }) => ({
  theme: themes.getById(themeId, state),
  bulletpoints: bulletpoints.getByTheme(themeId, state),
  contributedBulletpoints: contributedBulletpoints.getByTheme(themeId, state),
  fetching: themes.singleFetching(themeId, state)
    || bulletpoints.referencedThemesFetching(themeId, state)
    || contributedBulletpoints.referencedThemesFetching(themeId, state)
    || bulletpoints.allFetching(themeId, state)
    || contributedBulletpoints.allFetching(themeId, state),
  getBulletpointById: (id: number) => bulletpoints.getById(themeId, id, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (id: number) => dispatch(single(id)),
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
  fetchBulletpoints: (theme: number) => dispatch(bulletpoint.allWithReferencedThemes(theme)),
  fetchContributedBulletpoints: (theme: number) => dispatch(contributedBulletpoint.all(theme)),
  changeRating: (
    themeId: number,
    bulletpointId: number,
    point: PointType,
  ) => rate(bulletpointId, point, () => dispatch(bulletpoint.updateSingle(themeId, bulletpointId))),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
