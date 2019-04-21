// @flow
import { isEmpty } from 'lodash';
import type { FetchedBulletpointType } from '../bulletpoint/types';
import * as bulletpoints from '../bulletpoint/selects';

export const fetchedAll = (theme: number, state: Object): boolean => (
  state.themeContributedBulletpoints[theme]
  && !isEmpty(state.themeContributedBulletpoints[theme].payload)
);
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (fetchedAll(theme, state)) {
    return state.themeContributedBulletpoints[theme].payload
      .map(bulletpoint => bulletpoints.withReferencedTheme(bulletpoint, state))
      .map(bulletpoint => bulletpoints.withComparedTheme(bulletpoint, state));
  }
  return [];
};
const referencedThemesFetching = (theme: number, state: Object): boolean => (
  bulletpoints.relatedThemesFetching(
    state,
    getByTheme(theme, state).map(bulletpoint => bulletpoint.referenced_theme_id),
  )
);
export const isFetching = (theme: number, state: Object): boolean => (
  isEmpty(state.themeContributedBulletpoints[theme])
  || state.themeContributedBulletpoints[theme].fetching
  || referencedThemesFetching(theme, state)
);
