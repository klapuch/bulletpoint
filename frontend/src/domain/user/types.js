// @flow
export type RoleType = 'member' | 'admin' | 'guest';
export type MeType = {|
  username: string,
  role: RoleType,
  email: string
|};
export type PostedUserType = {|
  +username: string,
|};
export type FetchedUserType = {|
  +username: string,
|};
export type ErrorUserType = {|
  +username: ?string,
|};
