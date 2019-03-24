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

// todo: extract
export const getAvatar = (user: FetchedUserType, width: number, height: number) => (
  `${process.env.REACT_APP_STATIC || ''}/${user.avatar_filename}?w=${width}&h=${height}`
);
