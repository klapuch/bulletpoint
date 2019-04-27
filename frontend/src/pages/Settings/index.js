// @flow
import React from 'react';
import UserForm from '../../domain/user/components/Form/HttpForm';
import AvatarForm from '../../domain/avatar/components/Form/HttpForm';

type Props = {|
  +history: Object,
|};
export default function ({ history }: Props) {
  return (
    <>
      <div className="row">
        <h1>Nastavení</h1>
      </div>
      <UserForm history={history} />
      <div className="row">
        <AvatarForm history={history} />
      </div>
    </>
  );
}
