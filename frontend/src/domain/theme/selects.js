// @flow
import { isEmpty } from 'lodash';
import memoizee from 'memoizee';
import type { FetchedThemeType } from './types';

export const requestedSingle = (id: number, state: Object): boolean => !!state.theme.single[id];

export const fetchedSingle = (id: number, state: Object): boolean => (
  requestedSingle(id, state) || (
    state.theme.single[id] ? !isEmpty(state.theme.single[id].payload) : false
  )
);

export const getById = (id: number, state: Object): FetchedThemeType|Object => (
  state.theme.single[id] ? state.theme.single[id].payload : {}
);

export const singleFetching = (id: number, state: Object): boolean => (
  state.theme.single[id] ? state.theme.single[id].fetching : true
);

export const allFetching = (state: Object): boolean => state.theme.all.fetching;

export const getAll = (state: Object): Array<Object> => state.theme.all.payload;

export const getCommonTag = memoizee((themes: Array<FetchedThemeType>, tagId: number) => {
  const tag = themes.map(theme => theme.tags)
    .reduce((prev, current) => current.concat(prev), [])
    .reduce((prev, current) => ({ [current.id]: current.name, ...prev }), {});
  return tag[tagId];
});

export const getTotal = (state: Object): number => state.theme.total;
