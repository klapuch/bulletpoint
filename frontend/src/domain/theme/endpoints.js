// @flow
import axios from 'axios';
import { forEach } from 'lodash';
import {
  invalidatedSingle,
  receivedAll,
  receivedSingle,
  requestedAll,
  requestedSingle,
  receivedUpdateSingle,
  requestedUpdateSingle,
} from './actions';
import * as themes from './selects';
import * as response from '../../api/response';
import type { FetchedThemeType, PostedThemeType } from './types';
import type { PaginationType } from '../../api/dataset/PaginationType';
import type { FetchedTagType } from '../tags/types';

export const fetchSingle = (
  id: number,
  flat: boolean = false,
) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (themes.fetchedSingle(id, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedSingle(id));
  return axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data)))
    .then(() => themes.getById(id, getState()).related_themes_id)
    .then((themeIds: Array<number>) => {
      if (!flat) {
        forEach(themeIds, themeId => dispatch(fetchSingle(themeId, true)));
      }
    });
};

export const updateSingle = (
  themeId: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(themeId);
  axios.get(`/themes/${themeId}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};

export const create = (theme: PostedThemeType, next: (number) => (void)) => (
  axios.post('/themes', theme)
    .then(response => response.headers)
    .then(headers => response.extractedLocationId(headers.location))
    .then(next)
);

export const starOrUnstar = (themeId: number, isStarred: boolean, next: (number) => (void)) => (
  axios.patch(`/themes/${themeId}`, { is_starred: isStarred })
    .then(next)
);

export const change = (
  id: number,
  theme: PostedThemeType,
) => (dispatch: (mixed) => Object) => (
  axios.put(`/themes/${id}`, theme)
    .then(() => dispatch(invalidatedSingle(id)))
);

const fetchAll = (
  params: Object,
  pagination: PaginationType = { page: 1, perPage: 10 },
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

export const fetchByTag = (
  tag: ?number,
  pagination: PaginationType,
) => (dispatch: (mixed) => Object) => (
  dispatch(fetchAll({ tag_id: [tag] }, pagination))
);

export const fetchRecent = (
  pagination: PaginationType,
) => (dispatch: (mixed) => Object) => (
  dispatch(fetchAll({ sort: '-created_at' }, pagination))
);

export const fetchStarred = (
  pagination: PaginationType,
  tagId: ?number,
) => (dispatch: (mixed) => Object) => (
  dispatch(fetchAll({ is_starred: 'true', tag_id: [tagId], sort: '-starred_at' }, pagination))
);

export const fetchSearches = (keyword: string) => (dispatch: (mixed) => Object) => (
  dispatch(fetchAll({ q: keyword }, { page: 1, perPage: 20 }))
);

const toReactSelectSearches = (themes: Array<FetchedThemeType>, except: Array<number>) => (
  themes.filter(theme => !except.includes(theme.id))
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
