// @flow
import { isEmpty } from 'lodash';

export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints.all[theme].payload)
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeBulletpoints.all[theme]
    ? state.themeBulletpoints.all[theme].fetching
    : false
);
export const getByTheme = (theme: number, state: Object): Object => (
  state.themeBulletpoints.all[theme] ? state.themeBulletpoints.all[theme].payload : {}
);
