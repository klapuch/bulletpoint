// @flow
import type { TagType } from '../tags/types';

export type FetchedThemeType = {|
  +id: number,
  +user_id: number,
  +tags: Array<TagType>,
  +name: string,
  +created_at: string,
  +reference: {|
    +url: string,
  |}
|};
export type PostedThemeType = {|
  +tags: Array<number>,
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
