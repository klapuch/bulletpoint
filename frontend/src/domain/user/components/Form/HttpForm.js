// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { MeType, PostedUserType } from '../../types';
import * as user from '../../actions';
import DefaultForm from './DefaultForm';
import { getMe } from '../../index';
import { receivedSuccess } from '../../../../ui/message/actions';

type Props = {|
  +edit: (PostedUserType, (Object) => Promise<any>) => (void),
  +receivedSuccess: (string) => (void),
  +history: Object,
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

  handleSubmit = (postedUser: PostedUserType) => this.props.edit(
    postedUser,
    () => Promise.resolve()
      .then(this.props.receivedSuccess('Uživatelské jméno bylo změneno'))
      .then(this.reload)
      .then(() => this.props.history.push(this.props.history.pathname)),
  );

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
  edit: (postedUser: PostedUserType, next) => dispatch(user.edit(postedUser, next)),
  receivedSuccess: (message: string) => dispatch(receivedSuccess(message)),
});
export default connect(null, mapDispatchToProps)(HttpForm);
