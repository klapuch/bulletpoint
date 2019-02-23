// @flow
import { isEmpty, first, isEqual } from 'lodash';
import * as themes from '../theme/selects';
import type { FetchedBulletpointType } from './types';

export const withReferencedTheme = (
  bulletpoint: FetchedBulletpointType,
  state: Object,
): FetchedBulletpointType => {
  bulletpoint.referenced_theme = bulletpoint.referenced_theme_id.map((referenced_theme_id) => {
    return themes.getById(referenced_theme_id, state); // eslint-disable-line
  });
  return bulletpoint;
};
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (state.themeBulletpoints.all[theme] && state.themeBulletpoints.all[theme].payload) {
    return state.themeBulletpoints.all[theme].payload
      .map(bulletpoint => withReferencedTheme(bulletpoint, state));
  }
  return [];
};
const referencedThemesFetching = (theme: number, state: Object): boolean => {
  return getByTheme(theme, state)
    .map(bulletpoint => bulletpoint.referenced_theme_id)
    .map(referencedThemeIds => referencedThemeIds.map(referencedThemeId => themes.singleFetching(referencedThemeId, state)))
    .filter(areFetching => areFetching.filter(isFetching => isFetching === true))
    .filter(areFetching => areFetching.length > 0)
    .filter(areFetching => isEqual(areFetching, [true]))
    .length > 0;
};
export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints.all[theme] ? state.themeBulletpoints.all[theme].payload : {})
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeBulletpoints.all[theme]
    ? state.themeBulletpoints.all[theme].fetching || referencedThemesFetching(theme, state)
    : referencedThemesFetching(theme, state)
);
export const getById = (
  theme: number,
  bulletpoint: number,
  state: Object,
): FetchedBulletpointType|Object => (
  withReferencedTheme(
    first(getByTheme(theme, state).filter(single => single.id === bulletpoint)),
    state,
  )
);
