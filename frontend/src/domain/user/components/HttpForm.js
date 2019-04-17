// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { PostedUserType } from '../types';
import * as user from '../endpoints';
import Form from './Form';
import * as message from '../../../ui/message/actions';
import { getMe } from '../index';

type Props = {|
  +edit: (PostedUserType, () => (void)) => (void),
  +history: Object,
|};
class HttpForm extends React.Component<Props> {
  handleSubmitSetting = (postedUser: PostedUserType) => {
    this.props.edit(
      postedUser,
      () => {
        user.reload().then(() => this.props.history.push('/settings'));
      },
    );
  };

  render() {
    const me = getMe();
    if (me === null) {
      return null;
    }
    return (
      <Form user={me} onSubmit={this.handleSubmitSetting} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  edit: (
    postedUser: PostedUserType,
    next: () => (void),
  ) => dispatch(user.edit(postedUser, () => {
    dispatch(message.receivedSuccess('Uživatelské jméno bylo změneno'));
    next();
  })),
});
export default connect(null, mapDispatchToProps)(HttpForm);
