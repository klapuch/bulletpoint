// @flow

import type { FetchedBulletpointType } from './types';

export const RECEIVED_THEME_BULLETPOINTS = 'RECEIVED_THEME_BULLETPOINTS';
export const REQUESTED_THEME_BULLETPOINTS = 'REQUESTED_THEME_BULLETPOINTS';
export const REQUESTED_THEME_BULLETPOINT_UPDATE = 'REQUESTED_THEME_BULLETPOINT_UPDATE';
export const RECEIVED_THEME_BULLETPOINT_UPDATE = 'RECEIVED_THEME_BULLETPOINT_UPDATE';
export const RECEIVED_THEME_BULLETPOINT_EXTEND = 'RECEIVED_THEME_BULLETPOINT_EXTEND';
export const REQUESTED_THEME_BULLETPOINT_EXTEND = 'REQUESTED_THEME_BULLETPOINT_EXTEND';
export const INVALIDATED_THEME_BULLETPOINTS = 'INVALIDATED_THEME_BULLETPOINTS';

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

export const requestedUpdateSingle = (theme: number) => ({
  type: REQUESTED_THEME_BULLETPOINT_UPDATE,
  theme,
  fetching: true,
});

export const receivedUpdateSingle = (replacement: FetchedBulletpointType) => ({
  type: RECEIVED_THEME_BULLETPOINT_UPDATE,
  theme: replacement.theme_id,
  bulletpoint: replacement.id,
  replacement,
  fetching: false,
});

export const requestedExtendSingle = (theme: number) => ({
  type: REQUESTED_THEME_BULLETPOINT_EXTEND,
  theme,
  fetching: true,
});

export const receivedExtendSingle = (replacement: FetchedBulletpointType) => ({
  type: RECEIVED_THEME_BULLETPOINT_EXTEND,
  theme: replacement.theme_id,
  bulletpoint: replacement.id,
  replacement,
  fetching: false,
});
