// @flow
import { isEmpty, values } from 'lodash';
import type { FetchedTagType } from './types';

export const fetchedAll = (
  state: Object,
): boolean => !isEmpty(state.tags.all.payload);
export const isFetching = (
  state: Object,
): boolean => state.tags.all.fetching;
export const getAll = (
  state: Object,
): Array<FetchedTagType> => values(state.tags.all.payload);

export const fetchedStarred = (
  state: Object,
): boolean => !isEmpty(state.tags.starred.payload);
export const isStarredFetching = (
  state: Object,
): boolean => state.tags.starred.fetching;
export const getStarred = (
  state: Object,
): Array<FetchedTagType> => values(state.tags.starred.payload);
