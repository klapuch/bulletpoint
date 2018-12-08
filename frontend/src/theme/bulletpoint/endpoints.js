// @flow
import axios from 'axios';
import {
  requestedAll,
  receivedAll,
} from './actions';

export const all = (theme: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  dispatch(requestedAll(theme));
  axios.get(`/themes/${theme}/bulletpoints`)
    .then(response => dispatch(receivedAll(theme, response.data)));
};