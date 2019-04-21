// @flow
import React from 'react';
import * as user from '../../user/endpoints';
import * as avatar from '../endpoints';
import { getMe, getAvatar } from '../../user';
import AvatarForm from './Form';

type Props = {|
  +history: Object,
|};
export default class extends React.Component<Props> {
  handleSubmit = (file: FormData) => (
    avatar.upload(file).then(() => {
      user.reload().then(() => this.props.history.push('/settings'));
    })
  );

  render() {
    const me = getMe();
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
