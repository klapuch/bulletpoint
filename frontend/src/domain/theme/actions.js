// @flow

import type { FetchedThemeType } from './types';
import * as response from '../../api/response';

export const RECEIVED_THEME = 'RECEIVED_THEME';
export const RECEIVED_THEMES = 'RECEIVED_THEMES';
export const REQUESTED_THEME = 'REQUESTED_THEME';
export const REQUESTED_THEMES = 'REQUESTED_THEMES';
export const RECEIVED_INVALIDATED_THEME = 'RECEIVED_INVALIDATED_THEME';
export const REQUESTED_THEME_UPDATE = 'REQUESTED_THEME_UPDATE';
export const RECEIVED_THEME_UPDATE = 'RECEIVED_THEME_UPDATE';
export const REQUESTED_THEME_STAR_CHANGE = 'REQUESTED_THEME_STAR_CHANGE';
export const RECEIVED_THEME_STAR_CHANGE = 'RECEIVED_THEME_STAR_CHANGE';

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
