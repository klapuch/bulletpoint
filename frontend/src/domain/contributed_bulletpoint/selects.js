// @flow
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../bulletpoint/types';
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
const referencedThemesFetching = (theme: number, state: Object): boolean => (
  bulletpoints.relatedThemesFetching(
    state,
    getByTheme(theme, state).map(bulletpoint => bulletpoint.referenced_theme_id),
  )
);
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
