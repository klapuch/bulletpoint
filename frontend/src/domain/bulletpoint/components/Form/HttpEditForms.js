// @flow
import React from 'react';
import { connect } from 'react-redux';
import { FORM_TYPE_DEFAULT } from './types';
import Form from './index';
import * as themes from '../../../theme/selects';
import * as bulletpoints from '../../selects';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../types';
import * as bulletpoint from '../../endpoints';
import * as theme from '../../../theme/endpoints';
import type { FormTypes } from './types';
import type { FetchedThemeType } from '../../../theme/types';

type Props = {|
  +fetchTheme: (number) => (void),
  +themeId: number,
  +fetchBulletpoints: (number) => (void),
  +theme: FetchedThemeType,
  +formType: FormTypes,
  +getBulletpoints: () => (Array<FetchedBulletpointType>),
  +fetching: boolean,
  +bulletpointId: number,
  +onCancelClick: () => (void),
  +editBulletpoint: (
    themeId: number,
    bulletpointId: number,
    PostedBulletpointType,
    next: (void) => (void),
  ) => (Promise<any>),
|};
class HttpEditForms extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.props.fetchTheme(this.props.themeId);
    this.props.fetchBulletpoints(this.props.themeId);
  };

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const { themeId, bulletpointId } = this.props;
    return this.props.editBulletpoint(themeId, bulletpointId, bulletpoint, this.reload);
  };

  render() {
    const {
      theme, bulletpointId, formType, fetching,
    } = this.props;
    if (fetching) {
      return null;
    }
    const bulletpoints = this.props.getBulletpoints();
    return (
      <>
        {
          bulletpoints.map(bulletpoint => (
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
  fetching: bulletpoints.allFetching(themeId, state) || themes.singleFetching(themeId, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (id: number) => dispatch(theme.fetchSingle(id)),
  fetchBulletpoints: (themeId: number) => dispatch(bulletpoint.fetchAll(themeId)),
  editBulletpoint: (
    themeId: number,
    bulletpointId: number,
    postedBulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(bulletpoint.edit(themeId, bulletpointId, postedBulletpoint, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(HttpEditForms);
