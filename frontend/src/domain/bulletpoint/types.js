// @flow
import { omit } from 'lodash';
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
  +group: {|
    +root_bulletpoint_id: number|null,
    +children_bulletpoints: Array<FetchedBulletpointType>,
  |},
  +created_at: string,
  +content: string,
  +theme_id: number,
  +referenced_theme_id: Array<number>,
  +compared_theme_id: Array<number>,
  referenced_theme: Array<FetchedThemeType>,
  compared_theme: Array<FetchedThemeType>,
|};
export type PostedBulletpointType = {|
  +source: {|
    +link: string,
    +type: SourceType,
  |},
  +group: {|
    +root_bulletpoint_id: number|null,
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
  +referenced_themes: ?string,
|};
export const fromFetchedToPosted = (bulletpoint: FetchedBulletpointType) => (
  omit(bulletpoint, ['id', 'rating', 'theme_id', 'referenced_theme', 'compared_theme', 'user_id', 'created_at'])
);
