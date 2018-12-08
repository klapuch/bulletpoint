// @flow
import axios from 'axios';
import {
  requestedSingle,
  receivedSingle,
} from './actions';
import { etchedSingle } from './selects';

export const single = (id: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  dispatch(requestedSingle(id));
  axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data, response.headers.etag)));
};