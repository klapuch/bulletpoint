// @flow

import axios from 'axios';
import type { MeType } from './types';
import * as message from '../../ui/message/actions';
import * as session from '../access/session';
import * as users from './selects';
import {
  receivedSingle,
  requestedSingle,
  requestedTags,
  receivedTags,
} from './actions';

export const fetchMe = (token: string, next: (MeType) => (Promise<any>|void)) => (
  axios.get('/users/me', { headers: { Authorization: `Bearer ${token}` } })
    .then(response => response.data)
    .then(next)
);

export const reload = (token: ?string) => {
  const userToken = token || session.getValue();
  if (userToken !== null && typeof userToken !== 'undefined') {
    return fetchMe(userToken, me => session.updateCredentials(me));
  }
  return Promise.resolve();
};

export const edit = (
  properties: Object,
  next: () => (void),
) => (dispatch: (mixed) => Object) => (
  axios.put('/users/me', properties)
    .then(next)
    .catch(error => dispatch(message.receivedApiError(error)))
);

export const fetchSingle = (
  userId: number,
) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (users.fetched(userId, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedSingle(userId));
  return axios.get(`/users/${userId}`)
    .then(response => response.data)
    .then(user => dispatch(receivedSingle(userId, user)))
    .catch(error => dispatch(message.receivedApiError(error)));
};

export const fetchTags = (
  userId: number,
  tagIds: Array<number>,
) => (dispatch: (mixed) => Object) => {
  dispatch(requestedTags(userId));
  return axios.get(`/users/${userId}/tags`, { params: { tag_id: tagIds } })
    .then(response => response.data)
    .then(tags => dispatch(receivedTags(userId, tags)))
    .catch(error => dispatch(message.receivedApiError(error)));
};
