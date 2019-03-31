// @flow
import { isEmpty, first, flatten } from 'lodash';
import * as themes from '../theme/selects';
import type { FetchedBulletpointType } from './types';

export const withReferencedTheme = (
  bulletpoint: FetchedBulletpointType,
  state: Object,
): FetchedBulletpointType => ({
  ...bulletpoint,
  referenced_theme: [
    ...bulletpoint.referenced_theme_id.map(
      referenced_theme_id => themes.getById(referenced_theme_id, state),
    ),
  ],
});
export const withComparedTheme = (
  bulletpoint: FetchedBulletpointType,
  state: Object,
): FetchedBulletpointType => ({
  ...bulletpoint,
  compared_theme: [
    ...bulletpoint.compared_theme_id.map(
      compared_theme_id => themes.getById(compared_theme_id, state),
    ),
  ],
});
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (state.themeBulletpoints[theme] && state.themeBulletpoints[theme].payload) {
    return state.themeBulletpoints[theme].payload
      .map(bulletpoint => withReferencedTheme(bulletpoint, state))
      .map(bulletpoint => withComparedTheme(bulletpoint, state));
  }
  return [];
};
export const relatedThemesFetching = (
  state: Object,
  themeIds: Array<Array<number>>,
): boolean => flatten(themeIds)
  .map(relatedThemeId => themes.singleFetching(relatedThemeId, state))
  .filter(Boolean)
  .length > 0;
const referencedThemesFetching = (theme: number, state: Object): boolean => (
  relatedThemesFetching(
    state,
    getByTheme(theme, state).map(bulletpoint => bulletpoint.referenced_theme_id),
  )
);
const comparedThemesFetching = (theme: number, state: Object): boolean => (
  relatedThemesFetching(
    state,
    getByTheme(theme, state).map(bulletpoint => bulletpoint.compared_theme_id),
  )
);
export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints[theme] ? state.themeBulletpoints[theme].payload : {})
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeBulletpoints[theme]
    ? state.themeBulletpoints[theme].fetching
      || referencedThemesFetching(theme, state)
      || comparedThemesFetching(theme, state)
    : referencedThemesFetching(theme, state)
);
export const getById = (
  theme: number,
  bulletpoint: number,
  state: Object,
): FetchedBulletpointType|Object => (
  withComparedTheme(
    withReferencedTheme(
      first(getByTheme(theme, state).filter(single => single.id === bulletpoint)),
      state,
    ),
    state,
  )
);
