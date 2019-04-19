// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_ADD, FORM_TYPE_DEFAULT } from './types';
import Form from './index';
import * as themes from '../../../theme/selects';
import * as bulletpoints from '../../selects';
import type { PostedBulletpointType } from '../../types';
import * as bulletpoint from '../../endpoints';
import * as user from '../../../user';
import * as contributedBulletpoint from '../../../contributed_bulletpoint/endpoints';
import * as theme from '../../../theme/endpoints';
import type { FetchedThemeType } from '../../../theme/types';
import type { FormTypes } from './types';

type Props = {|
  +fetchTheme: () => (void),
  +fetchBulletpoints: () => (void),
  +fetchContributedBulletpoints: () => (void),
  +theme: FetchedThemeType,
  +fetching: boolean,
  +onCancelClick: () => (void),
  +onFormTypeChange: (FormTypes) => (void),
  +addBulletpoint: (PostedBulletpointType, (void) => (void)) => (Promise<any>),
|};
class HttpAddForm extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchTheme();
    this.props.fetchBulletpoints();
    this.props.fetchContributedBulletpoints();
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => (
    this.props.addBulletpoint(bulletpoint, this.reload)
      .then(() => this.props.onFormTypeChange(FORM_TYPE_DEFAULT))
  );

  render() {
    const { theme, fetching } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <Form
        theme={theme}
        onCancelClick={this.props.onCancelClick}
        type={FORM_TYPE_ADD}
        onSubmit={this.handleSubmit}
      />
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  theme: themes.getById(themeId, state),
  getBulletpoints: () => (bulletpoints.getByTheme(themeId, state)),
  fetching: bulletpoints.allFetching(themeId, state) || themes.singleFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  fetchTheme: () => dispatch(theme.fetchSingle(themeId)),
  fetchBulletpoints: () => dispatch(bulletpoint.fetchAll(themeId)),
  fetchContributedBulletpoints: () => dispatch(contributedBulletpoint.fetchAll(themeId)),
  addBulletpoint: (
    postedBulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(
    user.isAdmin()
      ? bulletpoint.add(themeId, postedBulletpoint, next)
      : contributedBulletpoint.add(themeId, postedBulletpoint, next),
  ),
  editBulletpoint: (
    bulletpointId: number,
    postedBulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(bulletpoint.edit(themeId, bulletpointId, postedBulletpoint, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(HttpAddForm);
