// @flow
export type FetchedBulletpointType = {|
  +id: number,
  +source: {|
    +link: string,
    +type: string,
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
    +type: string,
  |},
  +content: string,
|};
