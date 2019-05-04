// @flow
import { isEmpty, flatten } from 'lodash';
import * as themes from '../theme/selects';
import type { FetchedBulletpointType } from './types';

export const orderByExpandBulletpoint = (
  bulletpoints: Array<FetchedBulletpointType>,
  bulletpointId: number|null,
) => {
  if (bulletpointId === null) {
    return bulletpoints;
  }
  const only = bulletpoints.filter(b => b.group.root_bulletpoint_id === bulletpointId);
  const clear = bulletpoints.filter(b => b.group.root_bulletpoint_id !== bulletpointId);
  const first = clear.findIndex(b => b.id === bulletpointId);
  const before = clear.filter((b, index) => index <= first);
  const after = clear.filter((b, index) => index > first);
  return [
    ...before,
    ...only,
    ...after,
  ];
};

export const withChildrenGroups = (
  bulletpoints: Array<FetchedBulletpointType>,
): Array<FetchedBulletpointType> => (
  bulletpoints.map(bulletpoint => ({
    ...bulletpoint,
    group: {
      ...bulletpoint.group,
      children_bulletpoints: bulletpoints.filter(
        b => b.group.root_bulletpoint_id === bulletpoint.id,
      ),
    },
  })).filter(
    bulletpoint => bulletpoint.group.children_bulletpoints.length > 0
      || bulletpoint.group.root_bulletpoint_id === null,
  )
);

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
export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints[theme])
);
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => {
  if (fetchedAll(theme, state)) {
    return state.themeBulletpoints[theme].payload
      .map(bulletpoint => withReferencedTheme(bulletpoint, state))
      .map(bulletpoint => withComparedTheme(bulletpoint, state))
      .map(bulletpoint => ({
        ...bulletpoint,
        group: { ...bulletpoint.group, children_bulletpoints: [] },
      }));
  }
  return [];
};
export const hasChildrens = (themeId: number, bulletpointId: number|null, state: Object) => (
  bulletpointId !== null && state.themeBulletpoints[themeId].payload
    .filter(bulletpoint => bulletpoint.group.root_bulletpoint_id === bulletpointId)
    .length > 0
);
export const getByThemePossibleRoots = (
  theme: number,
  state: Object,
): Array<FetchedBulletpointType> => (
  getByTheme(theme, state).filter(bulletpoint => (bulletpoint.group.root_bulletpoint_id === null))
);
export const getByThemeGrouped = (theme: number, state: Object): Array<FetchedBulletpointType> => (
  withChildrenGroups(getByTheme(theme, state))
);
export const withExpanded = (
  bulletpoints: Array<FetchedBulletpointType>,
  expandBulletpointId: number|null,
): Array<FetchedBulletpointType> => {
  const expansions = [];
  bulletpoints.forEach((bulletpoint) => {
    expansions.push(bulletpoint);
    if (bulletpoint.id === expandBulletpointId) {
      expansions.push(...bulletpoint.group.children_bulletpoints);
    }
  });
  return expansions;
};
export const getByThemeExpanded = (
  theme: number,
  expandBulletpointId: number|null,
  state: Object,
): Array<FetchedBulletpointType> => (
  withExpanded(withChildrenGroups(getByTheme(theme, state)), expandBulletpointId)
);
export const relatedThemesFetching = (
  state: Object,
  themeIds: Array<Array<number>>,
): boolean => flatten(themeIds)
  .map(relatedThemeId => themes.isFetching(relatedThemeId, state))
  .filter(Boolean)
  .length > 0;

const referencedThemesFetching = (theme: number, state: Object): boolean => (
  relatedThemesFetching(
    state,
    getByThemeGrouped(theme, state).map(bulletpoint => bulletpoint.referenced_theme_id),
  )
);
const comparedThemesFetching = (theme: number, state: Object): boolean => (
  relatedThemesFetching(
    state,
    getByThemeGrouped(theme, state).map(bulletpoint => bulletpoint.compared_theme_id),
  )
);
export const isFetching = (theme: number, state: Object): boolean => (
  isEmpty(state.themeBulletpoints[theme])
  || state.themeBulletpoints[theme].fetching
  || referencedThemesFetching(theme, state)
  || comparedThemesFetching(theme, state)
);
