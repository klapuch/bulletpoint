// @flow
import {
  RECEIVED_THEME_CONTRIBUTED_BULLETPOINTS,
  REQUESTED_THEME_CONTRIBUTED_BULLETPOINTS,
  INVALIDATED_THEME_CONTRIBUTED_BULLETPOINTS,
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
    default:
      return state;
  }
};
