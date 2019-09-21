// @flow
import React from 'react';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../../../types';
import Boxes from '../Boxes';
import * as contributedBulletpoints from '../../../../contributed_bulletpoint/selects';
import * as contributedBulletpoint from '../../../../contributed_bulletpoint/actions';
import ContributionBox from './ContributionBox';
import SkeletonBoxes from '../Skeleton/SkeletonBoxes';
import * as themes from '../../../../theme/selects';
import type { FetchedThemeType } from '../../../../theme/types';

type Props = {|
  +fetching: boolean,
  +contributedBulletpoints: Array<FetchedBulletpointType>,
  +themeId: number,
  +theme: FetchedThemeType,
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
    const { fetching, theme, contributedBulletpoints } = this.props;
    if (!fetching && isEmpty(contributedBulletpoints)) {
      return null;
    }
    return (
      <>
        <h2 id="contributed_bulletpoints">Navrhnuté bulletpointy</h2>
        {fetching ? (<SkeletonBoxes show={!theme.is_empty}>{1}</SkeletonBoxes>) : (
          <Boxes bulletpoints={contributedBulletpoints}>
            {bulletpoint => (
              <ContributionBox
                key={`contribution-${bulletpoint.id}`}
                onDeleteClick={this.reload}
                bulletpoint={bulletpoint}
              />
            )}
          </Boxes>
        )}
      </>
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  theme: themes.getById(themeId, state),
  contributedBulletpoints: contributedBulletpoints.getByTheme(themeId, state),
  fetching: contributedBulletpoints.isFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  fetchContributedBulletpoints: () => dispatch(contributedBulletpoint.fetchAll(themeId)),
});

export default connect(mapStateToProps, mapDispatchToProps)(ContributionBoxes);
