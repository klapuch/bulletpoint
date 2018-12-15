// @flow
import {
  REQUESTED_THEME,
  REQUESTED_THEMES,
  RECEIVED_THEME,
  RECEIVED_THEMES,
  INVALIDATED_THEME,
} from './actions';

type State = {|
  +single: Object,
  +all: {|
    payload: Array<Object>,
    fetching: boolean,
  |},
|};
const init = {
  single: {},
  all: {
    payload: [],
    fetching: true,
  },
};
export default (state: State = init, action: Object): State => {
  switch (action.type) {
    case RECEIVED_THEME:
      return {
        ...state,
        single: {
          ...state.single,
          [action.id]: {
            payload: action.theme,
            fetching: action.fetching,
          },
        },
      };
    case RECEIVED_THEMES:
      return {
        ...state,
        all: {
          payload: action.themes,
          fetching: action.fetching,
        },
      };
    case REQUESTED_THEME:
      return {
        ...state,
        single: {
          ...state.single,
          [action.id]: {
            payload: {},
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_THEMES:
      return {
        ...state,
        all: {
          fetching: action.fetching,
        }
      };
    case INVALIDATED_THEME:
      return {
        ...state,
        single: {
          [action.id]: {
            payload: {},
            fetching: true,
          },
        },
      };
    default:
      return state;
  }
};
