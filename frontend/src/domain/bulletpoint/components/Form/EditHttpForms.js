// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_DEFAULT, FORM_TYPE_EDIT } from './types';
import Form from './DefaultForm';
import * as themes from '../../../theme/selects';
import * as bulletpoints from '../../selects';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../types';
import * as bulletpoint from '../../actions';
import type { FormTypes } from './types';
import type { FetchedThemeType } from '../../../theme/types';

type Props = {|
  +fetchBulletpoints: () => (void),
  +theme: FetchedThemeType,
  +getBulletpoints: () => (Array<FetchedBulletpointType>),
  +fetching: boolean,
  +bulletpointId: number,
  +onCancelClick: () => (void),
  +onFormTypeChange: (FormTypes) => (void),
  +editBulletpoint: (PostedBulletpointType, () => void) => (void),
|};
class EditHttpForms extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchBulletpoints();
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => (
    this.props.editBulletpoint(bulletpoint, () => {
      this.props.onFormTypeChange(FORM_TYPE_DEFAULT);
      this.reload();
    })
  );

  render() {
    const {
      theme,
      bulletpointId,
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
                  ? FORM_TYPE_EDIT
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
    next: () => void,
  ) => dispatch(bulletpoint.edit(themeId, bulletpointId, postedBulletpoint, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(EditHttpForms);
