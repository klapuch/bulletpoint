// @flow
import {
  RECEIVED_PAGINATION,
  RECEIVED_INIT_PAGING,
} from './actions';

export default (state: Object = {}, action: Object): Object => {
  switch (action.type) {
    case RECEIVED_PAGINATION:
      return {
        ...state,
        [action.source]: {
          ...state[action.source],
          pagination: action.pagination,
        },
      };
    case RECEIVED_INIT_PAGING:
      return {
        ...state,
        [action.source]: {
          pagination: action.pagination,
          ...state[action.source],
        },
      };
    default:
      return state;
  }
};
