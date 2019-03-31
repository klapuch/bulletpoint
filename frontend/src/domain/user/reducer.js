// @flow
import {
  RECEIVED_USER,
  REQUESTED_USER,
  REQUESTED_USER_TAGS,
  RECEIVED_USER_TAGS,
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
    case RECEIVED_USER_TAGS:
      return {
        ...state,
        [action.userId]: {
          ...state[action.userId],
          tags: {
            payload: action.tags,
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_USER_TAGS:
      return {
        ...state,
        [action.userId]: {
          ...state[action.userId],
          tags: {
            payload: [],
            fetching: action.fetching,
          },
        },
      };
    default:
      return state;
  }
};
