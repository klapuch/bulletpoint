// @flow
import axios from 'axios';
import {
  requestedSingle,
  receivedSingle,
} from './actions';
import { fetchedSingle } from './selects';
import * as response from '../api/response';

export const single = (id: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedSingle(id, getState())) return;
  dispatch(requestedSingle(id));
  axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data)));
};

export const create = (theme: Object, next: (number) => (void)) => {
  axios.post('/themes', theme)
    .then(response => response.headers)
    .then(headers => response.extractedLocationId(headers.location))
    .then(next);
};
