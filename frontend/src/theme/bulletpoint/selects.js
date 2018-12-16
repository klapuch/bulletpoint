// @flow
import { isEmpty, first } from 'lodash';
import type { FetchedBulletpointType } from './endpoints';

export const fetchedAll = (theme: number, state: Object): boolean => (
  !isEmpty(state.themeBulletpoints.all[theme] ? state.themeBulletpoints.all[theme].payload : {})
);
export const allFetching = (theme: number, state: Object): boolean => (
  state.themeBulletpoints.all[theme]
    ? state.themeBulletpoints.all[theme].fetching
    : false
);
export const getByTheme = (theme: number, state: Object): Array<FetchedBulletpointType> => (
  state.themeBulletpoints.all[theme] ? state.themeBulletpoints.all[theme].payload : []
);
export const getById = (
  theme: number,
  bulletpoint: number,
  state: Object,
): FetchedBulletpointType|Object => (
  first(getByTheme(theme, state).filter(single => single.id === bulletpoint))
);
