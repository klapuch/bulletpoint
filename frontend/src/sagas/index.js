// @flow
import { all, takeEvery, takeLatest } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import { FETCH_SINGLE_USER, FETCH_USER_TAGS, EDIT_USER } from '../domain/user/actions';
import * as user from '../domain/user/endpoints';

export default function* (): Saga {
  yield all([
    takeEvery(FETCH_USER_TAGS, user.fetchTags),
    takeEvery(FETCH_SINGLE_USER, user.fetchSingle),
    takeLatest(EDIT_USER, user.edit),
  ]);
}
