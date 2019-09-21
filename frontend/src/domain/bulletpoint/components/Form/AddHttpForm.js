// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_ADD, FORM_TYPE_DEFAULT } from './types';
import Form from './DefaultForm';
import * as themes from '../../../theme/selects';
import * as bulletpoints from '../../selects';
import type { PostedBulletpointType } from '../../types';
import * as bulletpoint from '../../actions';
import * as user from '../../../user';
import * as contributedBulletpoint from '../../../contributed_bulletpoint/actions';
import * as contributedBulletpoints from '../../../contributed_bulletpoint/selects';
import type { FetchedThemeType } from '../../../theme/types';
import type { FormTypes } from './types';

type Props = {|
  +fetchBulletpoints: () => (void),
  +fetchContributedBulletpoints: () => (void),
  +theme: FetchedThemeType,
  +fetching: boolean,
  +onCancelClick: () => (void),
  +onFormTypeChange: (FormTypes) => (void),
  +addBulletpoint: (PostedBulletpointType, () => Promise<any>) => (void),
|};
class AddHttpForm extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchBulletpoints();
    this.props.fetchContributedBulletpoints();
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const next = () => Promise.resolve()
      .then(() => this.props.onFormTypeChange(FORM_TYPE_DEFAULT))
      .then(this.reload);
    this.props.addBulletpoint(bulletpoint, next);
  };

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
  fetching: bulletpoints.isFetching(themeId, state)
    || contributedBulletpoints.isFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  fetchBulletpoints: () => dispatch(bulletpoint.fetchAll(themeId)),
  fetchContributedBulletpoints: () => dispatch(contributedBulletpoint.fetchAll(themeId)),
  addBulletpoint: (postedBulletpoint: PostedBulletpointType, next) => dispatch(
    user.isAdmin()
      ? bulletpoint.add(themeId, postedBulletpoint, next)
      : contributedBulletpoint.add(themeId, postedBulletpoint, next),
  ),
});
export default connect(mapStateToProps, mapDispatchToProps)(AddHttpForm);
