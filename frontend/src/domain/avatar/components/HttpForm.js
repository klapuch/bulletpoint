// @flow
import React from 'react';
import { connect } from 'react-redux';
import * as user from '../../user/endpoints';
import * as avatar from '../endpoints';
import { getMe, getAvatar } from '../../user';
import AvatarForm from './Form';
import * as message from '../../../ui/message/actions';
import type { MeType } from '../../user/types';

type Props = {|
  +upload: (FormData) => Promise<void>,
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

  handleSubmit = (file: FormData) => this.props.upload(file)
    .then(user.reload)
    .then(this.reload)
    // $FlowFixMe correct string from endpoint.js
    .catch(this.props.receivedError);

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
        <AvatarForm onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  receivedError: error => dispatch(message.receivedError(error)),
  upload: (file: FormData) => avatar.upload(file),
});
export default connect(null, mapDispatchToProps)(HttpForm);
