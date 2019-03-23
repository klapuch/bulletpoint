// @flow
export type RoleType = 'member' | 'admin' | 'guest';
export type MeType = {|
  username: string,
  role: RoleType,
  email: string,
  avatar_filename: string,
|};
export type PostedUserType = {|
  +username: string,
|};
export type FetchedUserType = {|
  +username: ?string,
|};
export type ErrorUserType = {|
  +username: ?string,
|};
export const fromFetchedToPosted = (user: FetchedUserType) => ({
  username: user.username || '',
});
