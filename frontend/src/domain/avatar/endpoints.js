// @flow
import axios from 'axios';
import { call, put } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import * as user from '../user/endpoints';
import { receivedApiError } from '../../ui/message/actions';

export function* upload(action: Object): Saga {
  try {
    yield call(axios.post, '/avatars', action.avatar);
    yield call(user.refresh);
    yield call(action.next);
  } catch (error) {
    yield put(receivedApiError(error));
  }
}
