// @flow
import axios from 'axios';
import {
  requestedAll,
  receivedAll,
} from './actions';
import { fetchedAll } from './selects';

export const all = (theme: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(theme, getState())) return;
  dispatch(requestedAll(theme));
  axios.get(`/themes/${theme}/bulletpoints`)
    .then(response => dispatch(receivedAll(theme, response.data)));
};
