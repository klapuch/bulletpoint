// @flow
export type PostedCredentialsType = {|
  +login: string,
  +password: string,
|};
export type ErrorCredentialsType = {|
  +login: ?string,
  +password: ?string,
|};
export type PostedProviderCredentialsType = {|
  +login: string,
|};
export const FACEBOOK_PROVIDER = 'facebook';
export const GOOGLE_PROVIDER = 'google';
export const INTERNAL_PROVIDER = 'internal';
export type ProviderTypes = 'facebook' | 'google' | 'internal';
