// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import * as sign from '../../../domain/sign/endpoints';

type Props = {|
  +signOut: (() => (void)) => (void),
  +history: Object,
|};
class Out extends React.PureComponent<Props> {
  componentWillMount() {
    this.props.signOut(() => this.props.history.push('/sign/in'));
  }

  render() {
    return null;
  }
}

const mapDispatchToProps = dispatch => ({
  signOut: (next: () => (void)) => dispatch(sign.signOut(next)),
});
export default connect(null, mapDispatchToProps)(Out);
