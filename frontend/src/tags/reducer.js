// @flow
import {
  RECEIVED_TAGS,
  REQUESTED_TAGS,
  INVALIDATED_TAGS,
} from './actions';

type State = {|
  +all: Object,
|};
const init = {
  all: {
    fetching: true,
    payload: null,
  },
};
export default (state: State = init, action: Object): State => {
  switch (action.type) {
    case RECEIVED_TAGS:
      return {
        ...state,
        all: {
          payload: action.tags,
          fetching: action.fetching,
        },
      };
    case REQUESTED_TAGS:
      return {
        ...state,
        all: {
          fetching: action.fetching,
        },
      };
    case INVALIDATED_TAGS:
      return {
        ...state,
        all: {
          fetching: true,
        },
      };
    default:
      return state;
  }
};
