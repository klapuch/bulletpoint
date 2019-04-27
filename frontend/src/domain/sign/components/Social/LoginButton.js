// @flow
import React from 'react';
import './LoginButton.css';

type Props = {|
  +onClick: (void) => (void),
  +provider: 'facebook' | 'google',
  +children: string,
|};
const LoginButton = ({ onClick, provider, children }: Props) => (
  <button type="button" onClick={onClick} className={`loginBtn loginBtn--${provider}`}>
    {children}
  </button>
);

export default LoginButton;
