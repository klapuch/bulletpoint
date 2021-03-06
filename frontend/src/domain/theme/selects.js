// @flow
import { isEmpty } from 'lodash';
import memoizee from 'memoizee';
import type { FetchedThemeType } from './types';
import type { HttpError } from '../../api/types';

export const fetchedSingle = (id: number, state: Object): boolean => (
  !isEmpty(state.theme.single[id])
);

export const withRelatedThemes = (
  theme: FetchedThemeType,
  state: Object,
): FetchedThemeType => ({
  ...theme,
  // eslint-disable-next-line
  related_themes: theme.related_themes_id.map(id => (
    // $FlowFixMe
    fetchedSingle(id, state) ? state.theme.single[id].payload : {}
  )),
});

export const getById = (id: number, state: Object): FetchedThemeType|Object => (
  fetchedSingle(id, state) && !isEmpty(state.theme.single[id].payload)
    ? withRelatedThemes(state.theme.single[id].payload, state)
    : {}
);

export const getError = (id: number, state: Object): ?HttpError => state.theme.errors[id];
export const hasError = (id: number, state: Object): boolean => !isEmpty(getError(id, state));

export const getTagIds = (theme: FetchedThemeType): Array<number> => (
  theme.tags.map(tag => tag.id)
);

export const isFetching = (id: number, state: Object): boolean => (
  isEmpty(state.theme.single[id]) || state.theme.single[id].fetching
);

export const isStarred = (id: number, state: Object): boolean => (
  !isEmpty(state.theme.stars[id])
    ? state.theme.stars[id].starred
    : state.theme.single[id].payload.is_starred
);

export const isAllFetching = (state: Object): boolean => state.theme.all.fetching;

export const getAll = (state: Object): Array<Object> => state.theme.all.payload;

export const getCommonTag = memoizee((themes: Array<FetchedThemeType>, tagId: number): string => {
  const tag = themes.map(theme => theme.tags)
    .reduce((prev, current) => current.concat(prev), [])
    .reduce((prev, current) => ({ [current.id]: current.name, ...prev }), {});
  return tag[tagId];
});

export const getTotal = (state: Object): number => state.theme.total;
