// @flow

import type { TagType } from './types';

export const RECEIVED_TAGS = 'RECEIVED_TAGS';
export const REQUESTED_TAGS = 'REQUESTED_TAGS';
export const INVALIDATED_TAGS = 'INVALIDATED_TAGS';

export const invalidatedAll = () => ({
  type: INVALIDATED_TAGS,
});

export const requestedAll = () => ({
  type: REQUESTED_TAGS,
  fetching: true,
});

export const receivedAll = (tags: Array<TagType>) => ({
  type: RECEIVED_TAGS,
  tags,
  fetching: false,
});
