// @flow

export const RECEIVED_THEME_BULLETPOINTS = 'RECEIVED_THEME_BULLETPOINTS';
export const REQUESTED_THEME_BULLETPOINTS = 'REQUESTED_THEME_BULLETPOINTS';
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

export const receivedAll = (theme: number, bulletpoints: Array<Object>) => ({
  type: RECEIVED_THEME_BULLETPOINTS,
  theme,
  bulletpoints,
  fetching: false,
});
