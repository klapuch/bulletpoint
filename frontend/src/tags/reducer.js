// @flow
import {
  RECEIVED_TAGS,
  REQUESTED_TAGS,
  INVALIDATED_TAGS,
} from './actions';
import type { TagType } from './types';

type State = {|
  +all: {|
    fetching: boolean,
    payload: Array<TagType|Object>,
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
    case INVALIDATED_TAGS:
      return {
        ...state,
        all: {
          fetching: true,
          payload: [],
        },
      };
    default:
      return state;
  }
};
