// @flow
import type { FetchedTagType } from '../tags/types';

export type FetchedThemeType = {|
  +id: number,
  +user_id: number,
  +is_starred: boolean,
  +tags: Array<FetchedTagType>,
  +alternative_names: Array<string>,
  +name: string,
  +created_at: string,
  +reference: {|
    +url: string,
  |}
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
