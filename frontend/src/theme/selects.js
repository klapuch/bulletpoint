// @flow
import { isEmpty } from 'lodash';
import type { FetchedThemeType } from './endpoints';

export const fetchedSingle = (id: number, state: Object): boolean => (
  state.theme.single[id] ? !isEmpty(state.theme.single[id].payload) : false
);

export const getById = (id: number, state: Object): Object => (
  state.theme.single[id] ? state.theme.single[id].payload : {}
);

export const singleFetching = (id: number, state: Object): boolean => (
  state.theme.single[id] ? state.theme.single[id].fetching : true
);

export const allFetching = (state: Object): boolean => state.theme.all.fetching;

export const getAll = (state: Object): FetchedThemeType => state.theme.all.payload;
