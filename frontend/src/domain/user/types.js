// @flow
export type RoleType = 'member' | 'admin' | 'guest';
export type FetchedUserType = {
  +username: string,
  +avatar_filename: string,
};
export type MeType = FetchedUserType & {
  +role: RoleType,
  +email: string,
};
export type PostedUserType = {|
  +username: string,
|};
export type ErrorUserType = {|
  +username: ?string,
|};
export const fromFetchedToPosted = (user: FetchedUserType) => ({
  username: user.username || '',
});
