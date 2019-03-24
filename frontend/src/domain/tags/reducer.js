// @flow
import {
  RECEIVED_TAGS,
  REQUESTED_TAGS,
} from './actions';
import type { FetchedTagType } from './types';

type State = {|
  starred: {
    fetching: boolean,
    payload: Array<FetchedTagType>,
  },
  all: {
    fetching: boolean,
    payload: Array<FetchedTagType>,
  },
|};
const init = {
  starred: {
    fetching: true,
    payload: [],
  },
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
        [action.filter]: {
          payload: action.tags,
          fetching: action.fetching,
        },
      };
    case REQUESTED_TAGS:
      return {
        ...state,
        [action.filter]: {
          fetching: action.fetching,
          payload: [],
        },
      };
    default:
      return state;
  }
};
