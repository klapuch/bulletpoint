// @flow
import { isEmpty } from 'lodash';
import type { FetchedUserType } from './types';

export const fetched = (id: number, state: Object): boolean => (
  !isEmpty(state.user[id]) && !isEmpty(state.user[id].payload)
);

export const getById = (
  id: number,
  state: Object,
): ?FetchedUserType => (!isEmpty(state.user[id]) ? state.user[id].payload : undefined);

export const isFetching = (
  id: number,
  state: Object,
): boolean => isEmpty(state.user[id]) || state.user[id].fetching;
