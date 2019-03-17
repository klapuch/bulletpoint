// @flow
import React from 'react';
import { connect } from 'react-redux';
import Form from '../../domain/user/components/Form';
import type { PostedUserType } from '../../domain/user/types';
import { getUsername } from '../../domain/user';
import * as user from '../../domain/user/endpoints';
import * as message from '../../ui/message/actions';

type Props = {|
  +edit: (PostedUserType, () => (void)) => (void),
  +history: Object,
|};

class Settings extends React.Component<Props> {
  handleSubmit = (postedUser: PostedUserType) => {
    this.props.edit(
      postedUser,
      () => {
        user.reload().then(() => this.props.history.push('/settings'));
      },
    );
  };

  render() {
    return (
      <>
        <div className="row">
          <h1>Nastavení</h1>
        </div>
        <Form user={{ username: getUsername() }} onSubmit={this.handleSubmit} />
      </>
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
export default connect(null, mapDispatchToProps)(Settings);
