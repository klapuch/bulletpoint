// @flow
import type { FetchedThemeType } from '../theme/types';

export type PointType = 1 | 0 | -1;
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
  +referenced_theme_id: Array<number>,
  referenced_theme: Array<FetchedThemeType>,
  compared_theme: Array<FetchedThemeType>,
|};
export type PostedBulletpointType = {|
  +source: {|
    +link: string,
    +type: SourceType,
  |},
  +content: string,
  +referenced_theme_id: Array<number>,
  +compared_theme_id: Array<number>,
  +referenced_theme?: Array<FetchedThemeType>,
  +compared_themes?: Array<FetchedThemeType>,
|};
export type ErrorBulletpointType = {|
  +source_link: ?string,
  +source_type: ?string,
  +content: ?string,
|};
