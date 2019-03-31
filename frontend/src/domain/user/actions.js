// @flow

import type { FetchedUserTagType, FetchedUserType } from './types';

export const RECEIVED_USER = 'RECEIVED_USER';
export const REQUESTED_USER = 'REQUESTED_USER';
export const RECEIVED_USER_TAGS = 'RECEIVED_USER_TAGS';
export const REQUESTED_USER_TAGS = 'REQUESTED_USER_TAGS';

export const requestedSingle = (userId: number) => ({
  type: REQUESTED_USER,
  userId,
  fetching: true,
});

export const receivedSingle = (userId: number, user: FetchedUserType) => ({
  type: RECEIVED_USER,
  userId,
  user,
  fetching: false,
});

export const requestedTags = (userId: number) => ({
  type: REQUESTED_USER_TAGS,
  userId,
  fetching: true,
});

export const receivedTags = (userId: number, tags: Array<FetchedUserTagType>) => ({
  type: RECEIVED_USER_TAGS,
  userId,
  tags,
  fetching: false,
});
