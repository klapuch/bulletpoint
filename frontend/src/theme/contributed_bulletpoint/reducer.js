// @flow
import {
  RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS,
  REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS,
  INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS,
  REQUESTED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE,
  RECEIVED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE,
} from './actions';

type State = {|
  +all: Object,
|};
const init = {
  all: {},
};
export default (state: State = init, action: Object): State => {
  switch (action.type) {
    case RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        all: {
          ...state.all,
          [action.theme]: {
            payload: action.bulletpoints,
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        all: {
          ...state.all,
          [action.theme]: {
            fetching: action.fetching,
          },
        },
      };
    case INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS:
      return {
        ...state,
        all: {
          ...state.all,
          [action.theme]: {
            fetching: true,
          },
        },
      };
    case RECEIVED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE:
      return {
        ...state,
        all: {
          ...state.all,
          [action.theme]: {
            payload: state.all[action.theme].payload.map(bulletpoint => (
              bulletpoint.id === action.bulletpoint ? action.replacement : bulletpoint
            )),
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_THEME_CONTRIBUTED_BULLETPOINT_UPDATE:
      return {
        ...state,
        all: {
          ...state.all,
          [action.theme]: {
            fetching: action.fetching,
          },
        },
      };
    default:
      return state;
  }
};
