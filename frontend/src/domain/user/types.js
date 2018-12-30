// @flow
export type RoleType = 'member' | 'admin' | 'guest';
export type MeType = {|
  username: string,
  role: RoleType,
  email: string
|};
