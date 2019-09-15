// @flow
import React from 'react';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import styled from 'styled-components';
import type { FetchedBulletpointType } from '../../../types';
import Boxes from '../Boxes';
import SkeletonBoxes from '../Skeleton/SkeletonBoxes';
import * as bulletpoints from '../../../selects';
import * as bulletpoint from '../../../actions';
import DetailBox from './DetailBox';
import * as themes from '../../../../theme/selects';
import type { FetchedThemeType } from '../../../../theme/types';

const GroupExpand = styled.span`
  font-size: 20px;
  margin: 9px;
  cursor: pointer;
`;

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
  highlightedBulletpointIds: Array<number>,
|};
const initState = {
  expandBulletpointId: null,
  highlightedBulletpointIds: [],
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
    const { history: { location: { state } } } = this.props;
    this.setState(prevState => ({
      ...prevState,
      expandBulletpointId: null,
      highlightedBulletpointIds: isEmpty(state) ? [] : state.highlightedBulletpointIds || [],
    }), () => {
      this.props.fetchBulletpoints();
      this.props.history.location.state = {};
    });
  };

  handleExpand = (expandBulletpointId: number) => this.setState({ expandBulletpointId });

  render() {
    const { fetching, theme } = this.props;
    const { highlightedBulletpointIds, expandBulletpointId } = this.state;
    if (fetching) {
      return <SkeletonBoxes show={!theme.is_empty}>{1}</SkeletonBoxes>;
    }
    return (
      <>
        <Boxes bulletpoints={this.props.getBulletpoints(expandBulletpointId)}>
          {bulletpoint => (
            <React.Fragment key={`bulletpoint-${bulletpoint.id}`}>
              <DetailBox
                onDeleteClick={this.reload}
                onEditClick={this.props.onEditClick}
                bulletpoint={bulletpoint}
                highlights={highlightedBulletpointIds}
              />
              {
                expandBulletpointId !== bulletpoint.id
                && !isEmpty(bulletpoint.group.children_bulletpoints)
                && (
                  <div className="text-center">
                    <GroupExpand
                      onClick={() => this.handleExpand(bulletpoint.id)}
                      className="glyphicon glyphicon glyphicon-option-horizontal"
                      aria-hidden="true"
                    />
                  </div>
                )
              }
            </React.Fragment>
          )}
        </Boxes>
      </>
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
