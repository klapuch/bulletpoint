// @flow

import axios from 'axios';
import type { MeType } from './types';
import * as message from '../../ui/message/actions';
import * as session from '../access/session';

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
