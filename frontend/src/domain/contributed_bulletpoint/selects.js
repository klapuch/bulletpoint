// @flow
import { isEmpty, isEqual } from 'lodash';
import type { FetchedBulletpointType } from '../bulletpoint/types';
import * as themes from '../theme/selects';
import * as bulletpoints from '../bulletpoint/selects';

export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (
    state.themeContributedBulletpoints.all[theme]
    && state.themeContributedBulletpoints.all[theme].payload
  ) {
    return state.themeContributedBulletpoints.all[theme].payload
      .map(bulletpoint => bulletpoints.withReferencedTheme(bulletpoint, state));
  }
  return [];
};
const referencedThemesFetching = (theme: number, state: Object): boolean => {
  return getByTheme(theme, state)
    .map(bulletpoint => bulletpoint.referenced_theme_id)
    .map(referencedThemeIds => (
      referencedThemeIds.map(referencedThemeId => themes.singleFetching(referencedThemeId, state))
    ))
    .filter(areFetching => areFetching.filter(isFetching => isFetching === true))
    .filter(areFetching => areFetching.length > 0)
    .filter(areFetching => isEqual(areFetching, [true]))
    .length > 0;
};
export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(
    state.themeContributedBulletpoints.all[theme]
      ? state.themeContributedBulletpoints.all[theme].payload
      : {},
  )
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeContributedBulletpoints.all[theme]
    ? state.themeContributedBulletpoints.all[theme].fetching
      || referencedThemesFetching(theme, state)
    : referencedThemesFetching(theme, state)
);
