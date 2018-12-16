// @flow
import { isEmpty, values } from 'lodash';
import type { TagType } from './types';

export const fetchedAll = (state: Object): boolean => !isEmpty(state.tags.all.payload);
export const allFetching = (state: Object): boolean => state.tags.all.fetching;
export const getAll = (state: Object): Array<TagType> => values(state.tags.all.payload);
