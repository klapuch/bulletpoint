// @flow
import React from 'react';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../types';
import Boxes from './Boxes';
import * as contributedBulletpoints from '../../contributed_bulletpoint/selects';
import * as contributedBulletpoint from '../../contributed_bulletpoint/endpoints';

type Props = {|
  +fetching: boolean,
  +contributedBulletpoints: Array<FetchedBulletpointType>,
  +themeId: number,
  +fetchContributedBulletpoints: (number) => (void),
  +deleteOne: (
    themeId: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => (void),
|};
type State = {|
  expandBulletpointId: number|null,
|};
const initState = {
  expandBulletpointId: null,
};
class UserBoxes extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.reload();
  }

  componentDidUpdate(prevProps: Props) {
    const { themeId } = this.props;
    if (prevProps.themeId !== themeId) {
      this.reload();
    }
  }

  reload = () => {
    this.props.fetchContributedBulletpoints(this.props.themeId);
  };

  handleDeleteClick = (bulletpointId: number) => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      this.props.deleteOne(this.props.themeId, bulletpointId, this.reload);
    }
  };

  render() {
    const { fetching, contributedBulletpoints } = this.props;
    if (fetching) {
      return null;
    }
    if (isEmpty(contributedBulletpoints)) {
      return null;
    }
    return (
      <>
        <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
        <Boxes
          bulletpoints={contributedBulletpoints}
          onDeleteClick={this.handleDeleteClick}
        />
      </>
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  contributedBulletpoints: contributedBulletpoints.getByTheme(themeId, state),
  fetching: contributedBulletpoints.allFetching(themeId, state),
});
const mapDispatchToProps = dispatch => ({
  fetchContributedBulletpoints: (
    themeId: number,
  ) => dispatch(contributedBulletpoint.fetchAll(themeId)),
  deleteOne: (
    themeId: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => dispatch(contributedBulletpoint.deleteOne(themeId, bulletpointId, next)),
});

export default connect(mapStateToProps, mapDispatchToProps)(UserBoxes);
