// @flow
import axios from 'axios';
import { call, put, select } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import type { MeType } from './types';
import * as session from '../access/session';
import * as users from './selects';
import {
  receivedSingle,
  requestedSingle,
  requestedTags,
  receivedTags,
} from './actions';
import { receivedApiError } from '../../ui/message/actions';

export const fetchMe = (token: string): Promise<MeType> => axios.get('/users/me', { headers: { Authorization: `Bearer ${token}` } })
  .then(response => response.data);

export const refresh = (token: ?string) => {
  const userToken = token || session.getValue();
  if (userToken !== null && userToken !== undefined) {
    return fetchMe(userToken).then(me => session.updateCredentials(me));
  }
  return Promise.resolve();
};


export function* edit(action: Object): Saga {
  try {
    yield call(axios.put, '/users/me', action.properties);
    yield call(refresh);
    yield call(action.next);
  } catch (error) {
    yield put(receivedApiError(error));
  }
}

export function* fetchSingle(action: Object): Saga {
  if (yield select(state => users.fetched(action.userId, state))) {
    return;
  }
  yield put(requestedSingle(action.userId));
  try {
    const response = yield call(axios.get, `/users/${action.userId}`);
    yield put(receivedSingle(action.userId, response.data));
  } catch (error) {
    yield put(receivedApiError(error));
  }
}

export function* fetchTags(action: Object): Saga {
  yield put(requestedTags(action.userId));
  try {
    const response = yield call(axios.get, `/users/${action.userId}/tags`, { params: { tag_id: action.tagIds } });
    yield put(receivedTags(action.userId, response.data));
  } catch (error) {
    yield put(receivedApiError(error));
  }
}
