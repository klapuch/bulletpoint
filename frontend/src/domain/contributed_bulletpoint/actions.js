// @flow

import type {FetchedBulletpointType, PostedBulletpointType} from '../bulletpoint/types';

export const RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS = 'RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS';
export const REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS = 'REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS';
export const INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS = 'INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS';
export const FETCH_ALL_CONTRIBUTED_BULLETPOINTS = 'FETCH_ALL_CONTRIBUTED_BULLETPOINTS';
export const DELETE_SINGLE_THEME_CONTRIBUTED_BULLETPOINT = 'DELETE_SINGLE_THEME_CONTRIBUTED_BULLETPOINT';
export const ADD_THEME_CONTRIBUTED_BULLETPOINT = 'ADD_THEME_CONTRIBUTED_BULLETPOINT';

export const add = (themeId: number, bulletpoint: PostedBulletpointType, next) => ({
  type: ADD_THEME_CONTRIBUTED_BULLETPOINT,
  themeId,
  bulletpoint,
  next,
});

export const fetchAll = (themeId: number) => ({
  type: FETCH_ALL_CONTRIBUTED_BULLETPOINTS,
  themeId,
});

export const deleteSingle = (themeId: number, bulletpointId: number, next) => ({
  type: DELETE_SINGLE_THEME_CONTRIBUTED_BULLETPOINT,
  themeId,
  bulletpointId,
  next,
});

export const invalidatedAll = (theme: number) => ({
  type: INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS,
  theme,
});

export const requestedAll = (theme: number) => ({
  type: REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS,
  theme,
  fetching: true,
});

export const receivedAll = (theme: number, bulletpoints: Array<FetchedBulletpointType>) => ({
  type: RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS,
  theme,
  bulletpoints,
  fetching: false,
});
