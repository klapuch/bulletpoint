// @flow
import type { PostedCredentialsType, ProviderTypes } from './types';

export const SIGN_IN = 'SIGN_IN';
export const SIGN_OUT = 'SIGN_OUT';

export const signIn = (
  provider: ProviderTypes,
  credentials: PostedCredentialsType,
  next: () => void,
) => ({
  type: SIGN_IN,
  provider,
  credentials,
  next,
});

export const signOut = (next: () => void) => ({
  type: SIGN_OUT,
  next,
});
