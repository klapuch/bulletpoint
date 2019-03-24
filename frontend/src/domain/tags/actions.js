// @flow

import type { FetchedTagType, FilterType } from './types';

export const RECEIVED_TAGS = 'RECEIVED_TAGS';
export const REQUESTED_TAGS = 'REQUESTED_TAGS';

const requestedCustom = (filter: FilterType) => ({
  type: REQUESTED_TAGS,
  fetching: true,
  filter,
});

const receivedCustom = (tags: Array<FetchedTagType>, filter: FilterType) => ({
  type: RECEIVED_TAGS,
  tags,
  fetching: false,
  filter,
});

export const requestedAll = () => requestedCustom('all');
export const receivedAll = (tags: Array<FetchedTagType>) => receivedCustom(tags, 'all');
export const requestedStarred = () => requestedCustom('starred');
export const receivedStarred = (tags: Array<FetchedTagType>) => receivedCustom(tags, 'starred');
