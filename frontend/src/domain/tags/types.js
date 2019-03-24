// @flow
export type FilterType = 'all' | 'starred';
export type FetchedTagType = {|
  +name: string,
  +id: number,
|};
export type PostedTagType = {|
  +name: string,
|};
export type ErrorTagType = {|
  +name: ?string,
|};
