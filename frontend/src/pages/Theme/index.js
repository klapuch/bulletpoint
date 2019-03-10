// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import * as bulletpoint from '../../domain/bulletpoint/endpoints';
import * as bulletpoints from '../../domain/bulletpoint/selects';
import * as contributedBulletpoint from '../../domain/contributed_bulletpoint/endpoints';
import * as contributedBulletpoints from '../../domain/contributed_bulletpoint/selects';
import * as themes from '../../domain/theme/selects';
import * as theme from '../../domain/theme/endpoints';
import * as user from '../../domain/user';
import Form from '../../domain/bulletpoint/components/Form';
import Loader from '../../ui/Loader';
import SlugRedirect from '../../router/SlugRedirect';
import type { FetchedBulletpointType, PostedBulletpointType, PointType } from '../../domain/bulletpoint/types';
import type { FetchedThemeType } from '../../domain/theme/types';
import type { FormTypes } from '../../domain/bulletpoint/components/Form/types';
import Boxes from '../../domain/bulletpoint/components/Boxes';
import Header from '../../domain/theme/components/Header';
import { FORM_TYPE_ADD, FORM_TYPE_DEFAULT, FORM_TYPE_EDIT } from '../../domain/bulletpoint/components/Form/types';
import AddButton from '../../domain/bulletpoint/components/Form/AddButton';

type State = {|
  formType: FormTypes,
  bulletpointId: number|null,
|};
type Props = {|
  +addBulletpoint: (themeId: number, PostedBulletpointType, (void) => (void)) => (Promise<any>),
  +editBulletpoint: (
    themeId: number,
    bulletpointId: number,
    PostedBulletpointType,
    next: (void) => (void),
  ) => (Promise<any>),
  +deleteBulletpoint: (
    themeId: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => (void),
  +bulletpoints: Array<FetchedBulletpointType>,
  +contributedBulletpoints: Array<FetchedBulletpointType>,
  +changeBulletpointRating: (themeId: number, bulletpoint: number, point: PointType) => (void),
  +starOrUnstar: (themeId: number, isStarred: boolean) => (void),
  +fetchBulletpoints: (number) => (void),
  +fetchContributedBulletpoints: (number) => (void),
  +fetchTheme: (number) => (void),
  +fetching: boolean,
  +getBulletpointById: (number) => FetchedBulletpointType,
  +match: Object,
  +theme: FetchedThemeType,
|};
const initState = {
  formType: FORM_TYPE_DEFAULT,
  bulletpointId: null,
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
    const { bulletpointId } = this.state;
    if (this.state.formType === FORM_TYPE_ADD) {
      return this.props.addBulletpoint(id, bulletpoint, this.reload);
    } else if (this.state.formType === FORM_TYPE_EDIT && bulletpointId !== null) {
      return this.props.editBulletpoint(id, bulletpointId, bulletpoint, this.reload);
    }
    return Promise.resolve();
  };

  reload = () => {
    this.setState(initState);
    const { match: { params: { id } } } = this.props;
    this.props.fetchTheme(id);
    this.props.fetchBulletpoints(id);
    if (user.isLoggedIn()) {
      this.props.fetchContributedBulletpoints(id);
    }
  };

  handleBulletpointRatingChange = (bulletpointId: number, point: PointType) => {
    const { match: { params: { id } } } = this.props;
    this.props.changeBulletpointRating(
      id,
      bulletpointId,
      this.props.getBulletpointById(bulletpointId).rating.user === point
        ? 0
        : point,
    );
  };

  handleStarClick = (isStarred: boolean) => {
    const { match: { params: { id } } } = this.props;
    this.props.starOrUnstar(id, isStarred);
  };

  handleDeleteClick = (bulletpointId: number) => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      const { match: { params: { id } } } = this.props;
      this.props.deleteBulletpoint(id, bulletpointId, this.reload);
    }
  };

  handleEditClick = (id: number) => this.setState({ formType: 'edit', bulletpointId: id });

  handleAddClick = () => this.setState({ formType: 'add' });

  handleCancelClick = () => this.setState(initState);

  render() {
    if (this.props.fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.props.theme.name}>
        <Helmet><title>{this.props.theme.name}</title></Helmet>
        <Header theme={this.props.theme} onStarClick={this.handleStarClick} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpoints">Bulletpointy</h2>
            <Boxes
              bulletpoints={this.props.bulletpoints}
              onRatingChange={this.handleBulletpointRatingChange}
              onEditClick={user.isAdmin() ? this.handleEditClick : undefined}
              onDeleteClick={user.isAdmin() ? this.handleDeleteClick : undefined}
            />
            {!isEmpty(this.props.contributedBulletpoints) && (
              <>
                <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
                <Boxes
                  bulletpoints={this.props.contributedBulletpoints}
                  onDeleteClick={this.handleDeleteClick}
                />
              </>
            )}
            {user.isLoggedIn() && (
              this.props.bulletpoints.map(bulletpoint => (
                <Form
                  key={bulletpoint.id}
                  theme={this.props.theme}
                  bulletpoint={bulletpoint}
                  onCancelClick={this.handleCancelClick}
                  type={
                    bulletpoint.id === this.state.bulletpointId
                      ? this.state.formType
                      : FORM_TYPE_DEFAULT
                  }
                  onSubmit={this.handleSubmit}
                />
              ))
            )}
            {user.isLoggedIn()
              && ![FORM_TYPE_ADD, FORM_TYPE_EDIT].includes(this.state.formType)
              && <AddButton onClick={this.handleAddClick} />
            }
            {this.state.formType === FORM_TYPE_ADD && (
              <Form
                theme={this.props.theme}
                onCancelClick={this.handleCancelClick}
                type={FORM_TYPE_ADD}
                onSubmit={this.handleSubmit}
              />
            )}
            {!isEmpty(this.props.theme.related_themes_id) && (
              <>
                <h2 id="related_themes">Související témata</h2>
                <div className="well">
                  {this.props.theme.related_themes.map((relatedTheme, order) => (
                    <React.Fragment key={order}>
                      {order === 0 ? '' : ', '}
                      <Link key={order} to={`/themes/${relatedTheme.id}/${getSlug(relatedTheme.name)}`}>
                        {relatedTheme.name}
                      </Link>
                    </React.Fragment>
                  ))}
                </div>
              </>
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
    || bulletpoints.allFetching(themeId, state)
    || contributedBulletpoints.allFetching(themeId, state),
  getBulletpointById: (id: number) => bulletpoints.getById(themeId, id, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (id: number) => dispatch(theme.single(id)),
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
  fetchBulletpoints: (themeId: number) => dispatch(bulletpoint.all(themeId)),
  starOrUnstar: (
    themeId: number,
    isStarred: boolean,
  ) => theme.starOrUnstar(themeId, isStarred, () => dispatch(theme.updateSingle(themeId))),
  fetchContributedBulletpoints: (themeId: number) => dispatch(contributedBulletpoint.all(themeId)),
  changeBulletpointRating: (
    themeId: number,
    bulletpointId: number,
    point: PointType,
  ) => bulletpoint.rate(
    bulletpointId,
    point,
    () => dispatch(bulletpoint.updateSingle(themeId, bulletpointId)),
  ),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
