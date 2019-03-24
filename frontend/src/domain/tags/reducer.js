// @flow
import {
  RECEIVED_TAGS,
  REQUESTED_TAGS,
} from './actions';
import type { FetchedTagType } from './types';

type State = {|
  fetching: boolean,
  payload: Array<FetchedTagType>,
|};
const init = {
  fetching: true,
  payload: [],
};
export default (state: State = init, action: Object): State => {
  switch (action.type) {
    case RECEIVED_TAGS:
      return {
        ...state,
        payload: action.tags,
        fetching: action.fetching,
      };
    case REQUESTED_TAGS:
      return {
        ...state,
        fetching: action.fetching,
        payload: [],
      };
    default:
      return state;
  }
};
