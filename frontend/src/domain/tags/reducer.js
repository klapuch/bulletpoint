// @flow
import {
  RECEIVED_TAGS,
  REQUESTED_TAGS,
} from './actions';
import type { FetchedTagType } from './types';

type State = {|
  +all: {|
    fetching: boolean,
    payload: Array<FetchedTagType|Object>,
  |},
|};
const init = {
  all: {
    fetching: true,
    payload: [],
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
          payload: [],
        },
      };
    default:
      return state;
  }
};
