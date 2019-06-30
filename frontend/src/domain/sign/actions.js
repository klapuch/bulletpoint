// @flow
import type {PostedCredentialsType, ProviderTypes} from "./types";

export const SIGN_IN = 'SIGN_IN';
export const SIGN_OUT = 'SIGN_OUT';

export const signIn = (
  provider: ProviderTypes,
  credentials: PostedCredentialsType,
  next,
) => ({
  type: SIGN_IN,
  provider,
  credentials,
  next,
});

export const signOut = (next) => ({
  type: SIGN_OUT,
  next,
});
