// @flow
import type { PointType } from '../bulletpoint_rating/types';
import type { FetchedThemeType } from '../theme/types';

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
    +user: PointType,
  |},
  +content: string,
  +theme_id: number,
  +referenced_theme_id: number | null,
  referenced_theme: FetchedThemeType | null,
|};
export type PostedBulletpointType = {|
  +source: {|
    +link: string,
    +type: SourceType,
  |},
  +content: string,
  +referenced_theme_id: number | null,
  +referenced_theme?: FetchedThemeType | null,
|};
export type ErrorBulletpointType = {|
  +source_link: ?string,
  +source_type: ?string,
  +content: ?string,
|};
