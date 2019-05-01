import React from 'react';
import { connect } from 'react-redux';
import Roots from './Roots';
import * as bulletpoints from '../../../selects';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../../types';

type Props = {|
  +onSelectChange: (Object) => (void),
  +hasChildrens: boolean,
  +bulletpoint: { id: number|null, ...PostedBulletpointType },
  +possibleRoots: Array<FetchedBulletpointType>
|};
class PossibleRoots extends React.PureComponent<Props> {
  render() {
    return (
      <Roots
        onSelectChange={this.props.onSelectChange}
        hasChildrens={this.props.hasChildrens}
        bulletpoint={this.props.bulletpoint}
        roots={this.props.possibleRoots}
      />
    );
  }
}

const mapStateToProps = (state, { themeId, bulletpoint: { id: bulletpointId } }) => ({
  possibleRoots: bulletpoints.getByThemePossibleRoots(themeId, state)
    .filter(bulletpoint => bulletpoint.id !== bulletpointId),
  hasChildrens: bulletpoints.hasChildrens(themeId, bulletpointId, state),
});
export default connect(mapStateToProps, null)(PossibleRoots);
