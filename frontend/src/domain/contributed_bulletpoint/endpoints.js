// @flow
import axios from 'axios';
import { call, put, select, all } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import { invalidatedAll, receivedAll, requestedAll } from './actions';
import { fetchedAll } from './selects';
import * as bulletpoints from '../bulletpoint/selects';
import * as theme from '../theme/actions';

export function* fetchAll(action: Object): Saga {
  if (yield select((state) => fetchedAll(action.themeId, state))) {
    return;
  }
  yield put(requestedAll(action.themeId));
  const response = yield call(axios.get, `/themes/${action.themeId}/contributed_bulletpoints`);
  yield put(receivedAll(action.themeId, response.data));
  const themeBulletpoints = yield select((state) => bulletpoints.getByTheme(action.themeId, state));
  yield all(
    themeBulletpoints
      .map(themeBulletpoint => ([...themeBulletpoint.referenced_theme_id]))
      .reduce((previous, current) => previous.concat(current), [])
      .map(relatedThemeId => put(theme.fetchSingle(relatedThemeId)))
  );
}

export function* add(action: Object): Saga {
  yield call(axios.post, `/themes/${action.themeId}/contributed_bulletpoints`, action.bulletpoint);
  yield put(invalidatedAll(action.themeId));
  yield call(action.next);
}

export function* deleteSingle(action: Object): Saga {
  yield call(axios.delete, `/contributed_bulletpoints/${action.bulletpointId}`);
  yield put(invalidatedAll(action.themeId));
  yield call(action.next);
}
