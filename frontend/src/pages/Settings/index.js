// @flow
import React from 'react';
import { connect } from 'react-redux';
import UserForm from '../../domain/user/components/Form';
import type { PostedUserType } from '../../domain/user/types';
import {getAvatar, getUsername} from '../../domain/user';
import * as user from '../../domain/user/endpoints';
import * as avatar from '../../domain/avatar/endpoints';
import * as message from '../../ui/message/actions';
import AvatarForm from '../../domain/avatar/components/Form';

type Props = {|
  +edit: (PostedUserType, () => (void)) => (void),
  +history: Object,
|};

class Settings extends React.Component<Props> {
  handleSubmitSetting = (postedUser: PostedUserType) => {
    this.props.edit(
      postedUser,
      () => {
        user.reload().then(() => this.props.history.push('/settings'));
      },
    );
  };

  handleSubmitAvatar = (file: FormData) => (
    avatar.upload(file).then(() => {
      user.reload().then(() => this.props.history.push('/settings'));
    })
  );

  render() {
    return (
      <>
        <div className="row">
          <h1>Nastavení</h1>
        </div>
        <UserForm user={{ username: getUsername() }} onSubmit={this.handleSubmitSetting} />
        <div className="row">
          <img src={`${getAvatar()}?w=100&h=100`} alt={getUsername()} className="img-thumbnail"/>
        </div>
        <AvatarForm onSubmit={this.handleSubmitAvatar} />
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
