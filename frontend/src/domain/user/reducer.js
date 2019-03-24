// @flow
import {
  RECEIVED_USER,
  REQUESTED_USER,
} from './actions';

export default (state: Object = {}, action: Object): {} => {
  switch (action.type) {
    case RECEIVED_USER:
      return {
        ...state,
        [action.userId]: {
          payload: action.user,
          fetching: action.fetching,
        },
      };
    case REQUESTED_USER:
      return {
        ...state,
        [action.userId]: {
          fetching: action.fetching,
        },
      };
    default:
      return state;
  }
};
