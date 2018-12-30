// @flow
export const SIGN_IN = 'SIGN_IN';
export const SIGN_OUT = 'SIGN_OUT';

export const requestedSignIn = () => ({
  type: SIGN_IN,
});

export const requestedSignOut = () => ({
  type: SIGN_OUT,
});
