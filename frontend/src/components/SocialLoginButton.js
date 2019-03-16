// @flow
import React from 'react';
import './SocialLoginButton.css';

type Props = {|
  +onClick: (void) => (void),
  +provider: 'facebook' | 'google',
  +children: string,
|};
const SocialLoginButton = ({ onClick, provider, children }: Props) => (
  <button type="button" onClick={onClick} className={`loginBtn loginBtn--${provider}`}>
    {children}
  </button>
);

export default SocialLoginButton;
