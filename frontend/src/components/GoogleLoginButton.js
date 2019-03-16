// @flow
import React from 'react';

type Props = {|
  +onClick: (void) => (void),
|};
const GoogleLoginButton = ({ onClick }: Props) => (
  <button type="button" onClick={onClick} className="loginBtn loginBtn--google">
    Login with Google&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  </button>
);

export default GoogleLoginButton;
