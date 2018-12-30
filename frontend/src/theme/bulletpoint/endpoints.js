// @flow
import axios from 'axios';
import {invalidatedAll, receivedAll, receivedUpdateSingle, requestedAll, requestedUpdateSingle} from './actions';
import { fetchedAll } from './selects';
import type { PostedBulletpointType } from './types';

export const all = (theme: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(theme, getState())) return;
  dispatch(requestedAll(theme));
  axios.get(`/themes/${theme}/bulletpoints`)
    .then(response => dispatch(receivedAll(theme, response.data)));
};

export const add = (
  theme: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.post(`/themes/${theme}/bulletpoints`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const deleteOne = (
  theme: number,
  bulletpoint: number,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.delete(`/bulletpoints/${bulletpoint}`)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const edit = (
  theme: number,
  id: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.put(`/bulletpoints/${id}`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const updateSingle = (
  theme: number,
  bulletpoint: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(theme, bulletpoint);
  axios.get(`/bulletpoints/${bulletpoint}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};
