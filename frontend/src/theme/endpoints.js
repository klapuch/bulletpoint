// @flow
import axios from 'axios';
import {
  receivedAll, receivedSingle, requestedAll, requestedSingle,
} from './actions';
import { fetchedSingle } from './selects';
import * as response from '../api/response';
import type { PostedThemeType } from './types';

export const single = (id: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedSingle(id, getState())) return;
  dispatch(requestedSingle(id));
  axios.get(`/themes/${id}`)
    .then(response => dispatch(receivedSingle(id, response.data)));
};

export const create = (theme: PostedThemeType, next: (number) => (void)) => {
  axios.post('/themes', theme)
    .then(response => response.headers)
    .then(headers => response.extractedLocationId(headers.location))
    .then(next);
};

const all = (params: Object) => (dispatch: (mixed) => Object) => {
  dispatch(requestedAll());
  axios.get('/themes', { params })
    .then(response => dispatch(receivedAll(response.data)));
};

export const allByTag = (tag: ?number) => (dispatch: (mixed) => Object) => (
  dispatch(all({ tag_id: tag }))
);

export const allRecent = () => (dispatch: (mixed) => Object) => (
  dispatch(all({ sort: '-created_at' }))
);
