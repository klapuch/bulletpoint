// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_ADD, FORM_TYPE_EDIT } from './types';
import AddButton from './AddButton';
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
  +fetchTheme: (number) => (void),
  +themeId: number,
  +fetchBulletpoints: (number) => (void),
  +theme: FetchedThemeType,
  +formType: FormTypes,
  +fetching: boolean,
  +onCancelClick: () => (void),
  +addBulletpoint: (themeId: number, PostedBulletpointType, (void) => (void)) => (Promise<any>),
  onAddClick: () => (void),
|};
class HttpAddForm extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchTheme(this.props.themeId);
    this.props.fetchBulletpoints(this.props.themeId);
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const { themeId } = this.props;
    return this.props.addBulletpoint(themeId, bulletpoint, this.reload);
  };

  render() {
    const { theme, formType, fetching } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <>
        {![FORM_TYPE_ADD, FORM_TYPE_EDIT].includes(formType)
          && <AddButton onClick={this.props.onAddClick} />}
        {formType === FORM_TYPE_ADD && (
          <Form
            theme={theme}
            onCancelClick={this.props.onCancelClick}
            type={FORM_TYPE_ADD}
            onSubmit={this.handleSubmit}
          />
        )}
      </>
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  theme: themes.getById(themeId, state),
  getBulletpoints: () => (bulletpoints.getByTheme(themeId, state)),
  fetching: bulletpoints.allFetching(themeId, state) || themes.singleFetching(themeId, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (id: number) => dispatch(theme.fetchSingle(id)),
  fetchBulletpoints: (themeId: number) => dispatch(bulletpoint.fetchAll(themeId)),
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
});
export default connect(mapStateToProps, mapDispatchToProps)(HttpAddForm);
