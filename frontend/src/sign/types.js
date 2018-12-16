// @flow
export type PostedCredentialsType = {|
  +email: string,
  +password: string,
|};
export type ErrorCredentialsType = {|
  +email: ?string,
  +password: ?string,
|};
