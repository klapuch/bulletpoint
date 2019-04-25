// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { FetchedBulletpointType } from '../types';
import Boxes from './Boxes';
import FakeBoxes from './FakeBoxes';
import * as bulletpoints from '../selects';
import * as bulletpoint from '../endpoints';
import DetailBox from './DetailBox';
import * as themes from '../../theme/selects';
import type { FetchedThemeType } from '../../theme/types';

type Props = {|
  +fetching: boolean,
  +history: Object,
  +themeId: number,
  +getBulletpoints: (number|null) => (Array<FetchedBulletpointType>),
  +fetchBulletpoints: () => (void),
  +onEditClick?: (number) => (void),
  +theme: FetchedThemeType,
|};
type State = {|
  expandBulletpointId: number|null,
|};
const initState = {
  expandBulletpointId: null,
};
class DetailBoxes extends React.Component<Props, State> {
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
    this.setState(initState, this.props.fetchBulletpoints);
  };

  handleExpandClick = (expandBulletpointId: number) => this.setState({ expandBulletpointId });

  render() {
    const { fetching, theme, history: { location: { state } } } = this.props;
    if (fetching) {
      return <FakeBoxes show={!theme.is_empty}>{3}</FakeBoxes>;
    }
    return (
      <Boxes
        box={DetailBox}
        onExpandClick={this.handleExpandClick}
        onEditClick={this.props.onEditClick}
        onDeleteClick={this.reload}
        highlights={
          typeof state !== 'undefined' && state.highlightedBulletpointIds
            ? state.highlightedBulletpointIds
            : []
        }
        bulletpoints={this.props.getBulletpoints(this.state.expandBulletpointId)}
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
  theme: themes.getById(themeId, state),
  fetching: bulletpoints.isFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  fetchBulletpoints: () => dispatch(bulletpoint.fetchAll(themeId)),
});

export default connect(mapStateToProps, mapDispatchToProps)(DetailBoxes);
