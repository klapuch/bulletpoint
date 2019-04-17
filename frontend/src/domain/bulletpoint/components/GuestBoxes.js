// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType } from '../types';
import Boxes from './Boxes';
import * as bulletpoints from '../selects';
import * as bulletpoint from '../endpoints';

type Props = {|
  +fetching: boolean,
  +history: Object,
  +themeId: number,
  +getBulletpoints: (number|null) => (Array<FetchedBulletpointType>),
  +fetchBulletpoints: (number) => (void),
|};
type State = {|
  expandBulletpointId: number|null,
|};
const initState = {
  expandBulletpointId: null,
};
class GuestBoxes extends React.Component<Props, State> {
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
    this.props.fetchBulletpoints(this.props.themeId);
  };

  handleExpand = (expandBulletpointId: number) => this.setState({ expandBulletpointId });

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
  fetching: bulletpoints.allFetching(themeId, state),
});
const mapDispatchToProps = dispatch => ({
  fetchBulletpoints: (themeId: number) => dispatch(bulletpoint.fetchAll(themeId)),
});

export default connect(mapStateToProps, mapDispatchToProps)(GuestBoxes);
