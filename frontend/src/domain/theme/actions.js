// @flow

import type { FetchedThemeType, PostedThemeType } from './types';
import * as response from '../../api/response';
import type { PaginationType } from '../../api/dataset/types';

export const RECEIVED_THEME = 'RECEIVED_THEME';
export const RECEIVED_THEMES = 'RECEIVED_THEMES';
export const REQUESTED_THEME = 'REQUESTED_THEME';
export const REQUESTED_THEMES = 'REQUESTED_THEMES';
export const RECEIVED_INVALIDATED_THEME = 'RECEIVED_INVALIDATED_THEME';
export const REQUESTED_THEME_UPDATE = 'REQUESTED_THEME_UPDATE';
export const RECEIVED_THEME_UPDATE = 'RECEIVED_THEME_UPDATE';
export const REQUESTED_THEME_STAR_CHANGE = 'REQUESTED_THEME_STAR_CHANGE';
export const RECEIVED_THEME_STAR_CHANGE = 'RECEIVED_THEME_STAR_CHANGE';
export const ERRORED_SINGLE_THEME = 'ERRORED_SINGLE_THEME';
export const FETCH_SINGLE_THEME = 'FETCH_SINGLE_THEME';
export const STAR_OR_UNSTAR_THEME = 'STAR_OR_UNSTAR_THEME';
export const CHANGE_THEME = 'CHANGE_THEME';
export const FETCH_ALL_THEMES = 'FETCH_ALL_THEMES';

export const fetchAll = (
  params: Object,
  pagination: PaginationType = { page: 1, perPage: 10 },
  next: () => void = () => {},
) => ({
  type: FETCH_ALL_THEMES,
  params,
  pagination,
  next,
});


export const fetchByTag = (
  tag: ?number,
  pagination: PaginationType,
  next: () => void = () => {},
) => (fetchAll({ tag_id: [tag] }, pagination, next));

export const fetchRecent = (
  pagination: PaginationType,
) => (fetchAll({ sort: '-created_at' }, pagination));

export const fetchStarred = (
  pagination: PaginationType,
  tagId: ?number,
) => (fetchAll({ is_starred: 'true', tag_id: [tagId], sort: '-starred_at' }, pagination));

export const fetchSearches = (
  keyword: string,
) => (fetchAll({ q: keyword }, { page: 1, perPage: 20 }));

export const change = (id: number, theme: PostedThemeType, next: () => void) => ({
  type: CHANGE_THEME,
  id,
  theme,
  next,
});

export const starOrUnstar = (themeId: number, is_starred: boolean) => ({
  type: STAR_OR_UNSTAR_THEME,
  themeId,
  is_starred,
});

export const fetchSingle = (id: number, flat: boolean = false) => ({
  type: FETCH_SINGLE_THEME,
  id,
  flat,
});

export const invalidatedSingle = (id: number) => ({
  type: RECEIVED_INVALIDATED_THEME,
  id,
});

export const requestedSingle = (id: number) => ({
  type: REQUESTED_THEME,
  id,
  fetching: true,
});

export const receivedSingle = (id: number, theme: FetchedThemeType) => ({
  type: RECEIVED_THEME,
  id,
  theme,
  fetching: false,
});

export const erroredSingle = (id: number, error: Object) => ({
  type: ERRORED_SINGLE_THEME,
  id,
  status: error.response.status,
  message: error.response.data.message,
});

export const requestedAll = () => ({
  type: REQUESTED_THEMES,
  fetching: true,
});

export const receivedAll = (themes: Array<FetchedThemeType>, headers: Object) => ({
  type: RECEIVED_THEMES,
  themes,
  total: response.extractedTotalCount(headers),
  fetching: false,
});

export const requestedUpdateSingle = (theme: number) => ({
  type: REQUESTED_THEME_UPDATE,
  theme,
  fetching: true,
});

export const receivedUpdateSingle = (replacement: FetchedThemeType) => ({
  type: RECEIVED_THEME_UPDATE,
  theme: replacement.id,
  replacement,
  fetching: false,
});

export const requestedStarChange = (theme: number, starred: boolean) => ({
  type: REQUESTED_THEME_STAR_CHANGE,
  theme,
  starred,
  fetching: true,
});

export const receivedStarChange = (theme: number, starred: boolean) => ({
  type: RECEIVED_THEME_STAR_CHANGE,
  theme,
  starred,
  fetching: false,
});
