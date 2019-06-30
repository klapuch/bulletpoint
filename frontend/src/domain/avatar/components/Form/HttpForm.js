// @flow
import React from 'react';
import { connect } from 'react-redux';
import * as avatar from '../../actions';
import { getMe, getAvatar } from '../../../user';
import DefaultForm from './DefaultForm';
import * as message from '../../../../ui/message/actions';
import type { MeType } from '../../../user/types';

type Props = {|
 +receivedError: (string),
|};
type State = {|
  me: MeType|null,
|};
const initState = {
  me: null,
};
class HttpForm extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.reload();
  }

  handleSubmit = (file: FormData, onAfterSubmit) => {
    const next = () => Promise.resolve()
      .then(this.reload)
      .then(onAfterSubmit)
      // $FlowFixMe correct string from endpoint.js
      .catch(this.props.receivedError);
    this.props.upload(file, next);
  };

  reload = () => {
    this.setState({ me: getMe() });
  };

  render() {
    const { me } = this.state;
    if (me === null) {
      return null;
    }
    return (
      <>
        <img src={getAvatar(me, 100, 100)} alt={me.username} className="img-thumbnail" />
        <DefaultForm onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  receivedError: error => dispatch(message.receivedError(error)),
  upload: (file: FormData, next) => dispatch(avatar.upload(file, next)),
});
export default connect(null, mapDispatchToProps)(HttpForm);
