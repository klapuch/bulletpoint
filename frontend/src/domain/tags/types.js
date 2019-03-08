// @flow
export type FetchedTagType = {|
  +name: string,
  +id: number,
|};
export type PostedTagType = {|
  +name: string,
|};
// TODO: Move
export type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};
export type ErrorTagType = {|
  +name: ?string,
|};
