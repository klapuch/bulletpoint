// @flow
import axios from 'axios';
import {
  call, put, select, all,
} from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import {
  invalidatedSingle,
  receivedAll,
  receivedSingle,
  requestedAll,
  requestedSingle,
  requestedStarChange,
  receivedStarChange,
  fetchSingle as fetchSingleAction,
  erroredSingle,
} from './actions';
import * as themes from './selects';
import * as response from '../../api/response';
import type { FetchedThemeType, PostedThemeType } from './types';
import type { FetchedTagType } from '../tags/types';
import { invalidatedStarred } from '../tags/actions';
import { receivedApiError } from '../../ui/message/actions';

export function* fetchSingle(action: Object): Saga {
  if (yield select(state => themes.fetchedSingle(action.id, state))) {
    return;
  }
  yield put(requestedSingle(action.id));
  try {
    const response = yield call(axios.get, `/themes/${action.id}`);
    yield put(receivedSingle(action.id, response.data));
    const relatedThemesId = yield select(
      state => themes.getById(action.id, state).related_themes_id,
    );
    if (!action.flat) {
      yield all(relatedThemesId.map(themeId => put(fetchSingleAction(themeId, true))));
    }
  } catch (error) {
    yield put(erroredSingle(action.id, error));
  }
}

export const create = (theme: PostedThemeType, next: (number) => void) => (
  axios.post('/themes', theme)
    .then(response => response.headers)
    .then(headers => response.extractedLocationId(headers.location))
    .then(next)
);

export function* starOrUnstar(action: Object): Saga {
  try {
    yield put(requestedStarChange(action.themeId, action.is_starred));
    yield call(axios.patch, `/themes/${action.themeId}`, { is_starred: action.is_starred });
    yield put(receivedStarChange(action.themeId, action.is_starred));
    yield put(invalidatedStarred());
  } catch (error) {
    yield put(receivedStarChange(action.themeId, !action.is_starred));
    yield put(receivedApiError(error));
  }
}

export function* change(action: Object): Saga {
  try {
    yield call(axios.put, `/themes/${action.id}`, action.theme);
    yield put(invalidatedSingle(action.id));
    yield put(fetchSingleAction(action.id));
    yield call(action.next);
  } catch (error) {
    yield put(receivedApiError(error));
  }
}

export function* fetchAll(action: Object): Saga {
  yield put(requestedAll());
  const response = yield call(
    axios.get,
    '/themes',
    {
      params: {
        page: action.pagination.page,
        per_page: action.pagination.perPage,
        ...action.params,
      },
    },
  );
  yield put(receivedAll(response.data, response.headers));
  yield call(action.next);
}

const toReactSelectSearches = (themes: Array<FetchedThemeType>, except: Array<number>) => (
  themes
    .filter(theme => !except.includes(theme.id))
    .map(theme => ({ label: theme.name, value: theme.id }))
);

export const fetchReactSelectSearches = (keyword: string, except: Array<number>): Promise<any> => (
  axios.get('/themes', { params: { q: keyword } })
    .then(response => response.data)
    .then(themes => toReactSelectSearches(themes, except))
);

export const fetchReactSelectTagSearches = (
  keyword: string,
  tags: Array<FetchedTagType>,
  except: Array<number>,
): Promise<any> => (
  axios.get('/themes', { params: { q: keyword, tag_id: tags.map(tag => tag.id) } })
    .then(response => response.data)
    .then(themes => toReactSelectSearches(themes, except))
);
