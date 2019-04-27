// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_DEFAULT } from './types';
import Form from './index';
import * as themes from '../../../theme/selects';
import * as bulletpoints from '../../selects';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../types';
import * as bulletpoint from '../../endpoints';
import type { FormTypes } from './types';
import type { FetchedThemeType } from '../../../theme/types';

type Props = {|
  +fetchBulletpoints: () => (void),
  +theme: FetchedThemeType,
  +formType: FormTypes,
  +getBulletpoints: () => (Array<FetchedBulletpointType>),
  +fetching: boolean,
  +bulletpointId: number,
  +onCancelClick: () => (void),
  +onFormTypeChange: (FormTypes) => (void),
  +editBulletpoint: (PostedBulletpointType) => (Promise<void>),
|};
class EditHttpForms extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchBulletpoints();
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => (
    this.props.editBulletpoint(bulletpoint)
      .then(() => this.props.onFormTypeChange(FORM_TYPE_DEFAULT))
      .then(this.reload)
  );

  render() {
    const {
      theme,
      bulletpointId,
      formType,
      fetching,
    } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <>
        {
          this.props.getBulletpoints().map(bulletpoint => (
            <Form
              key={bulletpoint.id}
              theme={theme}
              bulletpoint={bulletpoint}
              onCancelClick={this.props.onCancelClick}
              type={
                bulletpoint.id === bulletpointId
                  ? formType
                  : FORM_TYPE_DEFAULT
              }
              onSubmit={this.handleSubmit}
            />
          ))}
      </>
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  theme: themes.getById(themeId, state),
  getBulletpoints: () => (bulletpoints.getByTheme(themeId, state)),
  fetching: bulletpoints.isFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId, bulletpointId }) => ({
  fetchBulletpoints: () => dispatch(bulletpoint.fetchAll(themeId)),
  editBulletpoint: (
    postedBulletpoint: PostedBulletpointType,
  ) => dispatch(bulletpoint.edit(themeId, bulletpointId, postedBulletpoint)),
});
export default connect(mapStateToProps, mapDispatchToProps)(EditHttpForms);
