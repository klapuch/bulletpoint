import React from 'react';
import Groups from "./Groups";
import * as bulletpoints from "../../../selects";
import type {PostedBulletpointType} from "../../../types";
import {connect} from "react-redux";

type Props = {|
  +onSelectChange: (Object) => (void),
  +bulletpoint: PostedBulletpointType,
|};
class PossibleGroups extends React.Component<Props> {
  render() {
    return (
      <Groups
        onSelectChange={this.props.onSelectChange}
        bulletpoint={this.props.bulletpoint}
        groups={this.props.possibleRoots}
      />
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  possibleRoots: bulletpoints.getByThemePossibleRoots(themeId, state),
});
export default connect(mapStateToProps, null)(PossibleGroups);
