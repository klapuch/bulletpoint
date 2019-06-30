// @flow

import type { FetchedTagType, FilterType, PostedTagType } from './types';

export const RECEIVED_TAGS = 'RECEIVED_TAGS';
export const REQUESTED_TAGS = 'REQUESTED_TAGS';
export const INVALIDATED_TAGS = 'INVALIDATED_TAGS';
export const FETCH_ALL_TAGS = 'FETCH_ALL_TAGS';
export const FETCH_STARRED_TAGS = 'FETCH_STARRED_TAGS';
export const ADD_TAG = 'ADD_TAG';

export const fetchAll = () => ({
  type: FETCH_ALL_TAGS,
});

export const fetchStarred = () => ({
  type: FETCH_STARRED_TAGS,
});

export const add = (tag: PostedTagType, next: () => void) => ({
  type: ADD_TAG,
  tag,
  next,
});

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

const invalidatedTags = (filter: FilterType) => ({
  type: INVALIDATED_TAGS,
  filter,
});

export const invalidatedAll = () => invalidatedTags('all');
export const invalidatedStarred = () => invalidatedTags('starred');
export const requestedAll = () => requestedCustom('all');
export const receivedAll = (tags: Array<FetchedTagType>) => receivedCustom(tags, 'all');
export const requestedStarred = () => requestedCustom('starred');
export const receivedStarred = (tags: Array<FetchedTagType>) => receivedCustom(tags, 'starred');
