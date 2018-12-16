// @flow
import {
  RECEIVED_API_ERROR,
  DISCARDED_MESSAGE,
  RECEIVED_SUCCESS,
} from './actions';

type State = {|
  +content: ?string,
  +type: ?string,
|};
const init = {
  content: null,
  type: null,
};
export default (state: State = init, action: Object): State => {
  switch (action.type) {
    case RECEIVED_API_ERROR:
    case RECEIVED_SUCCESS:
    case DISCARDED_MESSAGE:
      return {
        ...state,
        content: action.content,
        type: action.type,
      };
    default:
      return state;
  }
};
