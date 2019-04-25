// @flow
import {
  REQUESTED_THEME,
  REQUESTED_THEMES,
  RECEIVED_THEME,
  RECEIVED_THEMES,
  RECEIVED_INVALIDATED_THEME,
  RECEIVED_THEME_UPDATE,
  REQUESTED_THEME_UPDATE,
  REQUESTED_THEME_STAR_CHANGE,
  RECEIVED_THEME_STAR_CHANGE,
} from './actions';

type State = {|
  +single: Object,
  +all: {|
    payload: Array<Object>,
    fetching: boolean,
  |},
  +stars: Object,
  +total: number,
|};
const init = {
  single: {},
  all: {
    payload: [],
    fetching: true,
  },
  stars: {},
  total: 0,
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
        total: action.total,
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
          payload: [],
        },
      };
    case RECEIVED_THEME_UPDATE:
      return {
        ...state,
        single: {
          ...state.single,
          [action.theme]: {
            payload: action.replacement,
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_THEME_UPDATE:
      return {
        ...state,
        single: {
          ...state.single,
          [action.theme]: {
            fetching: action.fetching,
          },
        },
      };
    case REQUESTED_THEME_STAR_CHANGE:
      return {
        ...state,
        stars: {
          ...state.stars,
          [action.theme]: {
            starred: action.starred,
            fetching: action.fetching,
          },
        },
      };
    case RECEIVED_THEME_STAR_CHANGE:
      return {
        ...state,
        stars: {
          ...state.stars,
          [action.theme]: {
            starred: action.starred,
            fetching: action.fetching,
          },
        },
      };
    case RECEIVED_INVALIDATED_THEME:
      return {
        ...state,
        single: {
          ...state.single,
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
