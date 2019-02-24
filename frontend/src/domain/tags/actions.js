// @flow

import type { FetchedTagType } from './types';

export const RECEIVED_TAGS = 'RECEIVED_TAGS';
export const REQUESTED_TAGS = 'REQUESTED_TAGS';

export const requestedAll = () => ({
  type: REQUESTED_TAGS,
  fetching: true,
});

export const receivedAll = (tags: Array<FetchedTagType>) => ({
  type: RECEIVED_TAGS,
  tags,
  fetching: false,
});
