// @flow
import { isEmpty, values } from 'lodash';

export const fetchedAll = (state: Object): boolean => !isEmpty(state.tags.all.payload);
export const allFetching = (state: Object): boolean => state.tags.all.fetching;
export const getAll = (state: Object): Array<Object> => values(state.tags.all.payload);
