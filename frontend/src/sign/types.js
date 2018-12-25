// @flow
export type PostedCredentialsType = {|
  +login: string,
  +password: string,
|};
export type ErrorCredentialsType = {|
  +login: ?string,
  +password: ?string,
|};
