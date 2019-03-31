// @flow
import {
  RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS,
  REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS,
  INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS,
} from './actions';

export default (state: Object = [], action: Object): Object => {
  switch (action.type) {
    case RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: action.bulletpoints,
          fetching: action.fetching,
        },
      };
    case REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: [],
          fetching: action.fetching,
        },
      };
    case INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: [],
          fetching: true,
        },
      };
    default:
      return state;
  }
};
