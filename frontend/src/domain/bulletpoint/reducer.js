// @flow
import {
  RECEIVED_THEME_BULLETPOINTS,
  REQUESTED_THEME_BULLETPOINTS,
  INVALIDATED_THEME_BULLETPOINTS,
  REQUESTED_THEME_BULLETPOINT_UPDATE,
  RECEIVED_THEME_BULLETPOINT_UPDATE,
} from './actions';

export default (state: Object = {}, action: Object): Object => {
  switch (action.type) {
    case RECEIVED_THEME_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: action.bulletpoints,
          fetching: action.fetching,
        },
      };
    case REQUESTED_THEME_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: [],
          fetching: action.fetching,
        },
      };
    case INVALIDATED_THEME_BULLETPOINTS:
      return {
        ...state,
        [action.theme]: {
          payload: [],
          fetching: true,
        },
      };
    case RECEIVED_THEME_BULLETPOINT_UPDATE:
      return {
        ...state,
        [action.theme]: {
          payload: state[action.theme].payload.map(bulletpoint => (
            bulletpoint.id === action.bulletpoint ? action.replacement : bulletpoint
          )),
          fetching: action.fetching,
        },
      };
    case REQUESTED_THEME_BULLETPOINT_UPDATE:
      return {
        ...state,
        [action.theme]: {
          payload: [],
          fetching: action.fetching,
        },
      };
    default:
      return state;
  }
};
