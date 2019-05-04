// @flow
import { omit } from 'lodash';
import type { FetchedTagType } from '../tags/types';

export type ReferenceType = {|
  +url: string,
  +is_broken: boolean,
|};

export type FetchedThemeType = {|
  +id: number,
  +user_id: number,
  +is_starred: boolean,
  +tags: Array<FetchedTagType>,
  +alternative_names: Array<string>,
  +related_themes_id: Array<number>,
  related_themes: Array<FetchedThemeType>,
  +name: string,
  +created_at: string,
  +is_empty: boolean,
  +reference: ReferenceType,
|};
export type PostedThemeType = {|
  +tags: Array<number>,
  +alternative_names: Array<string>,
  +name: string,
  +reference: {|
    +url: string,
  |}
|};
export type ErrorThemeType = {|
  +tags: ?string,
  +name: ?string,
  +reference_url: ?string,
|};

export const fromFetchedToPosted = (theme: FetchedThemeType) => ({
  ...omit(theme, ['id', 'user_id', 'is_starred', 'starred_at', 'tags', 'related_themes_id', 'related_themes', 'created_at', 'is_empty', 'reference.is_broken']),
  tags: theme.tags.map(tag => tag.id),
});
