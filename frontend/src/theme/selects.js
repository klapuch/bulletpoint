// @flow
import { isEmpty } from 'lodash';

export const fetchedSingle = (id: number, state: Object): boolean => (
  state.theme.single[id] ? !isEmpty(state.theme.single[id].payload) : false
);

export const getById = (id: string, state: Object): Object => (
  state.theme.single[id] ? state.theme.single[id].payload : {}
);

export const singleFetching = (id: string, state: Object): boolean => (
  state.theme.single[id] ? state.theme.single[id].fetching : true
);
