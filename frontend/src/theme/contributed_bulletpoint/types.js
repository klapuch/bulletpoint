// @flow
export type SourceType = 'head' | 'web';
export type FetchedBulletpointType = {|
  +id: number,
  +source: {|
    +link: string,
    +type: SourceType,
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