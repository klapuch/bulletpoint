// @flow

import type { FetchedBulletpointType, PointType, PostedBulletpointType } from './types';

export const RECEIVED_THEME_BULLETPOINTS = 'RECEIVED_THEME_BULLETPOINTS';
export const REQUESTED_THEME_BULLETPOINTS = 'REQUESTED_THEME_BULLETPOINTS';
export const REQUESTED_THEME_BULLETPOINT_UPDATE = 'REQUESTED_THEME_BULLETPOINT_UPDATE';
export const RECEIVED_THEME_BULLETPOINT_UPDATE = 'RECEIVED_THEME_BULLETPOINT_UPDATE';
export const INVALIDATED_THEME_BULLETPOINTS = 'INVALIDATED_THEME_BULLETPOINTS';
export const FETCH_ALL_BULLETPOINTS = 'FETCH_ALL_BULLETPOINTS';
export const ADD_THEME_BULLETPOINT = 'ADD_THEME_BULLETPOINT';
export const EDIT_THEME_BULLETPOINT = 'EDIT_THEME_BULLETPOINT';
export const UPDATE_SINGLE_THEME_BULLETPOINT = 'UPDATE_SINGLE_THEME_BULLETPOINT';
export const DELETE_SINGLE_THEME_BULLETPOINT = 'DELETE_SINGLE_THEME_BULLETPOINT';
export const RATE_SINGLE_THEME_BULLETPOINT = 'RATE_SINGLE_THEME_BULLETPOINT';

export const rate = (bulletpointId: number, themeId: number, point: PointType) => ({
  type: RATE_SINGLE_THEME_BULLETPOINT,
  bulletpointId,
  themeId,
  point,
});

export const deleteSingle = (themeId: number, bulletpointId: number, next) => ({
  type: DELETE_SINGLE_THEME_BULLETPOINT,
  themeId,
  bulletpointId,
  next,
});

export const updateSingle = (themeId: number, bulletpointId: number) => ({
  type: UPDATE_SINGLE_THEME_BULLETPOINT,
  themeId,
  bulletpointId,
});

export const edit = (
  themeId: number,
  bulletpointId: number,
  bulletpoint: PostedBulletpointType,
) => ({
  type: EDIT_THEME_BULLETPOINT,
  themeId,
  bulletpointId,
  bulletpoint,
});

export const add = (themeId: number, bulletpoint: PostedBulletpointType, next) => ({
  type: ADD_THEME_BULLETPOINT,
  themeId,
  bulletpoint,
  next,
});

export const fetchAll = (themeId: number) => ({
  type: FETCH_ALL_BULLETPOINTS,
  themeId,
});

export const invalidatedAll = (theme: number) => ({
  type: INVALIDATED_THEME_BULLETPOINTS,
  theme,
});

export const requestedAll = (theme: number) => ({
  type: REQUESTED_THEME_BULLETPOINTS,
  theme,
  fetching: true,
});

export const receivedAll = (theme: number, bulletpoints: Array<FetchedBulletpointType>) => ({
  type: RECEIVED_THEME_BULLETPOINTS,
  theme,
  bulletpoints,
  fetching: false,
});

export const receivedUpdateSingle = (replacement: FetchedBulletpointType) => ({
  type: RECEIVED_THEME_BULLETPOINT_UPDATE,
  theme: replacement.theme_id,
  bulletpoint: replacement.id,
  replacement,
  fetching: false,
});
