// @flow

import type {FetchedUserType} from "./types";

export const RECEIVED_USER = 'RECEIVED_USER';
export const REQUESTED_USER = 'REQUESTED_USER';

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
