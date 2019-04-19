// @flow
import React from 'react';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../types';
import Boxes from './Boxes';
import * as contributedBulletpoints from '../../contributed_bulletpoint/selects';
import * as contributedBulletpoint from '../../contributed_bulletpoint/endpoints';
import ContributionBox from './ContributionBox';

type Props = {|
  +fetching: boolean,
  +contributedBulletpoints: Array<FetchedBulletpointType>,
  +themeId: number,
  +fetchContributedBulletpoints: () => (void),
|};
class ContributionBoxes extends React.Component<Props> {
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
    this.props.fetchContributedBulletpoints();
  };

  render() {
    const { fetching, contributedBulletpoints } = this.props;
    if (fetching || isEmpty(contributedBulletpoints)) {
      return null;
    }
    return (
      <>
        <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
        <Boxes
          box={ContributionBox}
          bulletpoints={contributedBulletpoints}
          onDeleteClick={this.reload}
        />
      </>
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  contributedBulletpoints: contributedBulletpoints.getByTheme(themeId, state),
  fetching: contributedBulletpoints.allFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  fetchContributedBulletpoints: () => dispatch(contributedBulletpoint.fetchAll(themeId)),
});

export default connect(mapStateToProps, mapDispatchToProps)(ContributionBoxes);
