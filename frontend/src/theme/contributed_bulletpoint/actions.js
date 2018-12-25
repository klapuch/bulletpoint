// @flow

import type { FetchedBulletpointType } from './types';

export const RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS = 'RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS';
export const REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS = 'REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS';
export const REQUESTED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE = 'REQUESTED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE';
export const RECEIVED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE = 'RECEIVED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE';
export const INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS = 'INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS';

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

export const requestedUpdateSingle = (theme: number, bulletpoint: number) => ({
  type: REQUESTED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE,
  theme,
  bulletpoint,
  fetching: true,
});

export const receivedUpdateSingle = (replacement: FetchedBulletpointType) => ({
  type: RECEIVED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE,
  theme: replacement.theme_id,
  bulletpoint: replacement.id,
  replacement,
  fetching: false,
});
