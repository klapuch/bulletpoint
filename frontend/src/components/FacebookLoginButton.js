// @flow
import React from 'react';

type Props = {|
  +onClick: (void) => (void),
|};
const FacebookLoginButton = ({ onClick }: Props) => (
  <button type="button" onClick={onClick} className="loginBtn loginBtn--facebook">
    Login with Facebook
  </button>
);

export default FacebookLoginButton;
