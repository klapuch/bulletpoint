// @flow
import { isEmpty } from 'lodash';
import type { FetchedUserTagType, FetchedUserType } from './types';

export const fetched = (id: number, state: Object): boolean => (
  !isEmpty(state.user[id])
);

export const getById = (
  id: number,
  state: Object,
): ?FetchedUserType => (isEmpty(state.user[id]) ? undefined : state.user[id].payload);

export const isFetching = (
  id: number,
  state: Object,
): boolean => isEmpty(state.user[id]) || state.user[id].fetching;

const getTags = (
  id: number,
  tagIds: Array<number>,
  state: Object,
): Array<FetchedUserTagType> => (
  isEmpty(state.user[id].tags)
    ? []
    : state.user[id].tags.payload
);

export const getSelectedTags = (
  id: number,
  tagIds: Array<number>,
  state: Object,
): Array<FetchedUserTagType> => (
  getTags(id, tagIds, state).filter(tag => tagIds.includes(tag.tag_id))
);
