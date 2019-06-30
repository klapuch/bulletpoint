// @flow
import axios from 'axios';
import {
  call, put, select, all,
} from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import {
  invalidatedAll,
  receivedAll,
  receivedUpdateSingle,
  requestedAll,
  updateSingle as updateSingleAction,
} from './actions';
import * as theme from '../theme/actions';
import * as bulletpoints from './selects';

export function* rate(action: Object): Saga {
  yield call(axios.patch, `/bulletpoints/${action.bulletpointId}`, { rating: { user: action.point } });
  yield put(updateSingleAction(action.themeId, action.bulletpointId));
}

export function* fetchAll(action: Object): Saga {
  if (yield select(state => bulletpoints.fetchedAll(action.themeId, state))) {
    return;
  }
  yield put(requestedAll(action.themeId));
  const response = yield call(axios.get, `/themes/${action.themeId}/bulletpoints`);
  yield put(receivedAll(action.themeId, response.data));
  const themeBulletpoints = yield select(state => bulletpoints.getByTheme(action.themeId, state));
  yield all(
    themeBulletpoints
      .map(themeBulletpoint => ([
        ...themeBulletpoint.referenced_theme_id,
        ...themeBulletpoint.compared_theme_id,
      ]))
      .reduce((previous, current) => previous.concat(current), [])
      .map(relatedThemeId => put(theme.fetchSingle(relatedThemeId))),
  );
}

export function* add(action: Object): Saga {
  yield call(axios.post, `/themes/${action.themeId}/bulletpoints`, action.bulletpoint);
  yield put(invalidatedAll(action.themeId));
  yield call(action.next);
}

export function* deleteSingle(action: Object): Saga {
  yield call(axios.delete, `/bulletpoints/${action.bulletpointId}`);
  yield put(invalidatedAll(action.themeId));
  yield call(action.next);
}

export function* edit(action: Object): Saga {
  yield call(axios.put, `/bulletpoints/${action.bulletpointId}`, action.bulletpoint);
  yield put(invalidatedAll(action.themeId));
}

export function* updateSingle(action: Object): Saga {
  const response = yield call(axios.get, `/bulletpoints/${action.bulletpointId}`);
  yield put(receivedUpdateSingle(response.data));
}
