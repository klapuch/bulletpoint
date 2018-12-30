// @flow
import { isEmpty, first } from 'lodash';
import type { FetchedBulletpointType } from './types';

export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeContributedBulletpoints.all[theme] ? state.themeContributedBulletpoints.all[theme].payload : {})
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeContributedBulletpoints.all[theme]
    ? state.themeContributedBulletpoints.all[theme].fetching
    : false
);
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => (
  state.themeContributedBulletpoints.all[theme] ? state.themeContributedBulletpoints.all[theme].payload : []
);
