// @flow

export const RECEIVED_THEME = 'RECEIVED_THEME';
export const REQUESTED_THEME = 'REQUESTED_THEME';
export const INVALIDATED_THEME = 'INVALIDATED_THEME';

export const invalidatedSingle = (id: number) => ({
  type: INVALIDATED_THEME,
  id,
});

export const requestedSingle = (id: number) => ({
  type: REQUESTED_THEME,
  id,
  fetching: true,
});

export const receivedSingle = (id: number, theme: Object) => ({
  type: RECEIVED_THEME,
  id,
  theme,
  fetching: false,
});
