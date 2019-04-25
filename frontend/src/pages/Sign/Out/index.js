// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import * as sign from '../../../domain/sign/endpoints';
import * as message from '../../../ui/message/actions';

type Props = {|
  +signOut: () => (Promise<void>),
  +history: Object,
|};
class Out extends React.PureComponent<Props> {
  componentWillMount() {
    this.props.signOut()
      .then(() => this.props.history.push('/sign/in'));
  }

  render() {
    return null;
  }
}

const mapDispatchToProps = dispatch => ({
  signOut: () => dispatch(sign.signOut())
    .then(() => dispatch(message.receivedSuccess('Byl jsi odhlášen.'))),
});
export default connect(null, mapDispatchToProps)(Out);
