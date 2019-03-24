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

export const single = (
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
      if (flat === false) {
        forEach(themeIds, themeId => dispatch(single(themeId, true)));
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

export const fetchStarred = (
  pagination: PaginationType,
) => (dispatch: (mixed) => Object) => (
  dispatch(all({ is_starred: 'true' }, pagination))
);

export const allSearched = (keyword: string) => (dispatch: (mixed) => Object) => (
  dispatch(all({ q: keyword }, { page: 1, perPage: 20 }))
);

const toReactSelectSearches = (themes: Array<FetchedThemeType>, except: Array<number>) => (
  themes.filter(theme => !except.includes(theme.id))
    .map(theme => ({ label: theme.name, value: theme.id }))
);

export const allReactSelectSearches = (keyword: string, except: Array<number>): Promise<any> => (
  axios.get('/themes', { params: { q: keyword } })
    .then(response => response.data)
    .then(themes => toReactSelectSearches(themes, except))
);

export const allReactSelectTagSearches = (
  keyword: string,
  tags: Array<FetchedTagType>,
  except: Array<number>,
): Promise<any> => (
  axios.get('/themes', { params: { q: keyword, tag_id: tags.map(tag => tag.id).join(',') } })
    .then(response => response.data)
    .then(themes => toReactSelectSearches(themes, except))
);
