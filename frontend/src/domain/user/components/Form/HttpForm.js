// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { MeType, PostedUserType } from '../../types';
import * as user from '../../endpoints';
import DefaultForm from './DefaultForm';
import * as message from '../../../../ui/message/actions';
import { getMe } from '../../index';

type Props = {|
  +edit: (PostedUserType) => (Promise<any>),
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

  handleSubmit = (postedUser: PostedUserType) => this.props.edit(postedUser)
    .then(user.refresh)
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
      <DefaultForm user={me} onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  receivedError: error => dispatch(message.receivedError(error)),
  edit: (postedUser: PostedUserType) => user.edit(postedUser)
    .then(() => dispatch(message.receivedSuccess('Uživatelské jméno bylo změneno'))),
});
export default connect(null, mapDispatchToProps)(HttpForm);
