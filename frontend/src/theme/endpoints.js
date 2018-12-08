// @flow
import axios from 'axios';
import {
  requestedSingle,
  receivedSingle,
} from './actions';
import { fetchedSingle } from './selects';

export const single = (id: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedSingle(id, getState())) return;
  dispatch(requestedSingle(id));
  axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data)));
};
