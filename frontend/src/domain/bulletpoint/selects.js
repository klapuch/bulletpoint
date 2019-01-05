// @flow
import { isEmpty, first } from 'lodash';
import * as themes from '../theme/selects';
import type { FetchedBulletpointType } from './types';

export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints.all[theme] ? state.themeBulletpoints.all[theme].payload : {})
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeBulletpoints.all[theme]
    ? state.themeBulletpoints.all[theme].fetching
    : false
);
export const referencedThemesFetching = (theme: number, state: Object): boolean => {
  return getByTheme(theme, state)
    .map(bulletpoint => bulletpoint.referenced_theme_id)
    .filter(referencedThemeId => referencedThemeId !== null)
    .map(referencedThemeId => themes.singleFetching(referencedThemeId, state))
    .filter(isFetching => isFetching === true)
    .length > 0;
};
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (state.themeBulletpoints.all[theme] && state.themeBulletpoints.all[theme].payload) {
    return state.themeBulletpoints.all[theme].payload.map((bulletpoint) => withReferencedTheme(bulletpoint, state));
  }
  return [];
};
export const getById = (
  theme: number,
  bulletpoint: number,
  state: Object,
): FetchedBulletpointType|Object => (
  withReferencedTheme(first(getByTheme(theme, state).filter(single => single.id === bulletpoint)))
);

const withReferencedTheme = (bulletpoint: FetchedBulletpointType, state: Object): FetchedBulletpointType => {
  if (bulletpoint.referenced_theme_id !== null) {
    bulletpoint.referenced_theme = themes.getById(bulletpoint.referenced_theme_id, state);
  }
  return bulletpoint;
};
