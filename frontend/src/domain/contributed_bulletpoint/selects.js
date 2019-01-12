// @flow
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../bulletpoint/types';
import * as themes from '../theme/selects';
import * as bulletpoints from '../bulletpoint/selects';

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
    : false
);
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
export const referencedThemesFetching = (theme: number, state: Object): boolean => {
  return getByTheme(theme, state)
    .map(bulletpoint => bulletpoint.referenced_theme_id)
    .filter(referencedThemeId => referencedThemeId !== null)
    // $FlowFixMe no null
    .map(referencedThemeId => themes.singleFetching(referencedThemeId, state))
    .filter(isFetching => isFetching === true)
    .length > 0;
};
