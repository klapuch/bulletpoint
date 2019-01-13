// @flow

import type { FetchedThemeType } from './types';
export const RECEIVED_THEME = 'RECEIVED_THEME';
export const RECEIVED_THEMES = 'RECEIVED_THEMES';
export const REQUESTED_THEME = 'REQUESTED_THEME';
export const REQUESTED_THEMES = 'REQUESTED_THEMES';
export const RECEIVED_INVALIDATED_THEME = 'RECEIVED_INVALIDATED_THEME';
export const REQUESTED_THEME_UPDATE = 'REQUESTED_THEME_UPDATE';
export const RECEIVED_THEME_UPDATE = 'RECEIVED_THEME_UPDATE';

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

export const receivedAll = (themes: Array<FetchedThemeType>) => ({
  type: RECEIVED_THEMES,
  themes,
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
