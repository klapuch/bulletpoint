// @flow
export type SourceType = 'head' | 'web';
export type FetchedBulletpointType = {|
  +id: number,
  +source: {|
    +link: string,
    +type: SourceType,
  |},
  +user_id: number,
  +rating: {|
    +up: number,
    +down: number,
    +total: number,
    +user: number,
  |},
  +content: string,
  +theme_id: number,
|};
export type PostedBulletpointType = {|
  +source: {|
    +link: string,
    +type: SourceType,
  |},
  +content: string,
|};
export type ErrorBulletpointType = {|
  +source_link: ?string,
  +source_type: ?string,
  +content: ?string,
|};
export type PointType = 1 | 0 | -1;
