// @flow

import axios from 'axios';
import type { MeType } from './types';
import * as session from '../access/session';
import * as users from './selects';
import {
  receivedSingle,
  requestedSingle,
  requestedTags,
  receivedTags,
} from './actions';

export const fetchMe = (token: string): Promise<MeType> => (
  axios.get('/users/me', { headers: { Authorization: `Bearer ${token}` } })
    .then(response => response.data)
);

export const reload = (token: ?string) => {
  const userToken = token || session.getValue();
  if (userToken !== null && typeof userToken !== 'undefined') {
    return fetchMe(userToken)
      .then(me => session.updateCredentials(me))
      .catch(session.destroy);
  }
  return Promise.resolve();
};

export const edit = (properties: Object) => (
  axios.put('/users/me', properties)
    .catch(error => Promise.reject(error.response.data.message))
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
    .catch(error => Promise.reject(error.response.data.message));
};

export const fetchTags = (
  userId: number,
  tagIds: Array<number>,
) => (dispatch: (mixed) => Object) => {
  dispatch(requestedTags(userId));
  return axios.get(`/users/${userId}/tags`, { params: { tag_id: tagIds } })
    .then(response => response.data)
    .then(tags => dispatch(receivedTags(userId, tags)))
    .catch(error => Promise.reject(error.response.data.message));
};
