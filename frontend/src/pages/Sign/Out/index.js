// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import * as sign from '../../../domain/sign/actions';
import * as message from '../../../ui/message/actions';

type Props = {|
  +signOut: (() => Promise<any>) => (void),
  +receivedSuccess: (message: string) => (void),
  +history: Object,
|};
class Out extends React.PureComponent<Props> {
  constructor(props) {
    super(props);
    const next = () => Promise.resolve()
      .then(() => props.receivedSuccess('Byl jsi odhlášen.'))
      .then(() => props.history.push('/sign/in'));
    props.signOut(next);
  }

  render() {
    return null;
  }
}

const mapDispatchToProps = dispatch => ({
  signOut: next => dispatch(sign.signOut(next)),
  receivedSuccess: (text: string) => dispatch(message.receivedSuccess(text)),
});
export default connect(null, mapDispatchToProps)(Out);
