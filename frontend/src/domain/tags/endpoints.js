// @flow
import axios from 'axios';
import { call, select, put } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import {
  receivedAll,
  requestedAll,
  receivedStarred,
  requestedStarred,
  invalidatedAll,
} from './actions';
import { fetchedAll, fetchedStarred } from './selects';
import { receivedApiError } from '../../ui/message/actions';

export function* fetchAll(): Saga {
  if (yield select(fetchedAll)) {
    return;
  }
  yield put(requestedAll());
  const response = yield call(axios.get, 'tags');
  yield put(receivedAll(response.data));
}

export function* fetchStarred(): Saga {
  if (yield select(fetchedStarred)) {
    return;
  }
  yield put(requestedStarred());
  const response = yield call(axios.get, 'starred_tags');
  yield put(receivedStarred(response.data));
}

export function* add(action: Object): Saga {
  try {
    yield call(axios.post, '/tags', action.tag);
    yield put(invalidatedAll());
    yield call(action.next);
  } catch (error) {
    yield put(receivedApiError(error));
  }
}
