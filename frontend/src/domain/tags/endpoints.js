// @flow
import axios from 'axios';
import { receivedAll, requestedAll } from './actions';
import { fetchedAll } from './selects';

export const all = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(getState())) return;
  dispatch(requestedAll());
  axios.get('tags')
    .then(response => dispatch(receivedAll(response.data)));
};