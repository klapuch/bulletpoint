// @flow
import {
  REQUESTED_THEME,
  RECEIVED_THEME,
  INVALIDATED_THEME,
} from './actions';

type State = {|
  +single: Object,
|};
const init = {
  single: {},
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
