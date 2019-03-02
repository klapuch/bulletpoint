// @flow
import axios from 'axios';
import {
  invalidatedSingle,
  receivedAll,
  receivedSingle,
  requestedAll,
  requestedSingle,
  receivedUpdateSingle,
  requestedUpdateSingle,
} from './actions';
import { fetchedSingle } from './selects';
import * as response from '../../api/response';
import type { PostedThemeType } from './types';
import type { PaginationType } from '../../api/dataset/PaginationType';

export const single = (id: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedSingle(id, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedSingle(id));
  return axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data)));
};

export const updateSingle = (
  themeId: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(themeId);
  axios.get(`/themes/${themeId}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};

export const create = (theme: PostedThemeType, next: (number) => (void)) => {
  axios.post('/themes', theme)
    .then(response => response.headers)
    .then(headers => response.extractedLocationId(headers.location))
    .then(next);
};

export const starOrUnstar = (themeId: number, isStarred: boolean, next: (number) => (void)) => (
  axios.patch(`/themes/${themeId}`, { is_starred: isStarred })
    .then(next)
);

export const change = (
  id: number,
  theme: PostedThemeType,
  next: () => (void),
) => (dispatch: (mixed) => Object) => {
  axios.put(`/themes/${id}`, theme)
    .then(() => dispatch(invalidatedSingle(id)))
    .then(next);
};

const all = (
  params: Object,
  pagination: ?PaginationType = { page: 1, perPage: 10 },
) => (dispatch: (mixed) => Object) => {
  dispatch(requestedAll());
  axios.get(
    '/themes', {
      params: {
        page: pagination.page,
        per_page: pagination.perPage,
        ...params,
      },
    },
  )
    .then(response => dispatch(receivedAll(response.data, response.headers)));
};

export const allByTag = (
  tag: ?number,
  pagination: PaginationType,
) => (dispatch: (mixed) => Object) => (
  dispatch(all({ tag_id: tag }, pagination))
);

export const allRecent = (
  pagination: PaginationType,
) => (dispatch: (mixed) => Object) => (
  dispatch(all({ sort: '-created_at' }, pagination))
);

export const allSearched = (keyword: string) => (dispatch: (mixed) => Object) => (
  dispatch(all({ q: keyword }))
);

export const allReactSelectSearches = (keyword: string, except: Array<number>): Promise<any> => (
  axios.get('/themes', { q: keyword })
    .then(response => response.data)
    .then(themes => themes.filter(theme => !except.includes(theme.id)))
    .then(themes => themes.map(theme => ({ label: theme.name, value: theme.id })))
);
