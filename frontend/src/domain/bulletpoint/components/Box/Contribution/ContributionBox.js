// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType } from '../../../types';
import DefaultBox from '../Default/DefaultBox';
import * as contributedBulletpoints from '../../../../contributed_bulletpoint/actions';

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +onDeleteClick?: () => (void),
  +deleteOne: (next?: (void) => (void)) => (void),
|};
type State = {|
  more: boolean,
  expand: boolean,
|};
class ContributionBox extends React.Component<Props, State> {
  handleDeleteClick = () => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      this.props.deleteOne(this.props.onDeleteClick);
    }
  };

  render() {
    return (
      <DefaultBox
        bulletpoint={this.props.bulletpoint}
        onDeleteClick={this.handleDeleteClick}
      />
    );
  }
}

const mapDispatchToProps = (dispatch, { bulletpoint: { id, theme_id } }) => ({
  deleteOne: (
    next: (void) => (void),
  ) => dispatch(contributedBulletpoints.deleteSingle(theme_id, id, next)),
});
export default connect(null, mapDispatchToProps)(ContributionBox);
