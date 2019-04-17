// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType, PointType } from '../types';
import Boxes from './Boxes';
import * as bulletpoints from '../selects';
import * as bulletpoint from '../endpoints';

type Props = {|
  +fetching: boolean,
  +history: Object,
  +themeId: number,
  +getBulletpoints: (number|null) => (Array<FetchedBulletpointType>),
  +changeRating: (themeId: number, bulletpoint: number, point: PointType) => (void),
  +fetchBulletpoints: (number) => (void),
  +getBulletpointById: (number) => FetchedBulletpointType,
  +onEditClick?: (number) => (void),
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
class AdminBoxes extends React.Component<Props, State> {
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
    const { themeId } = this.props;
    this.props.fetchBulletpoints(themeId);
  };

  handleExpand = (expandBulletpointId: number) => this.setState({ expandBulletpointId });

  handleRatingChange = (bulletpointId: number, point: PointType) => {
    const { themeId } = this.props;
    this.props.changeRating(
      themeId,
      bulletpointId,
      this.props.getBulletpointById(bulletpointId).rating.user === point
        ? 0
        : point,
    );
  };

  handleDeleteClick = (bulletpointId: number) => {
    if (window.confirm('Opravdu chceš tento bulletpoint smazat?')) {
      this.props.deleteOne(this.props.themeId, bulletpointId, this.reload);
    }
  };

  render() {
    const { fetching, history: { location: { state } } } = this.props;
    if (fetching) {
      return null;
    }
    const bulletpoints = this.props.getBulletpoints(this.state.expandBulletpointId);
    return (
      <Boxes
        onExpand={this.handleExpand}
        highlights={
          typeof state !== 'undefined' && state.highlightedBulletpointIds
            ? state.highlightedBulletpointIds
            : []
        }
        bulletpoints={bulletpoints}
        onRatingChange={this.handleRatingChange}
        onEditClick={this.props.onEditClick}
        onDeleteClick={this.handleDeleteClick}
      />
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  getBulletpoints: (expandBulletpointId: number|null) => (
    expandBulletpointId === null
      ? bulletpoints.getByThemeGrouped(themeId, state)
      : bulletpoints.getByThemeExpanded(themeId, expandBulletpointId, state)
  ),
  getBulletpointById: (id: number) => bulletpoints.getById(themeId, id, state),
  fetching: bulletpoints.allFetching(themeId, state),
});
const mapDispatchToProps = dispatch => ({
  fetchBulletpoints: (themeId: number) => dispatch(bulletpoint.fetchAll(themeId)),
  changeRating: (
    themeId: number,
    bulletpointId: number,
    point: PointType,
  ) => bulletpoint.rate(
    bulletpointId,
    point,
    () => dispatch(bulletpoint.updateSingle(themeId, bulletpointId)),
  ),
  deleteOne: (
    themeId: number,
    bulletpointId: number,
    next: (void) => (void),
  ) => dispatch(bulletpoint.deleteOne(themeId, bulletpointId, next)),
});

export default connect(mapStateToProps, mapDispatchToProps)(AdminBoxes);
