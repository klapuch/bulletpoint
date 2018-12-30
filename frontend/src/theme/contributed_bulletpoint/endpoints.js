// @flow
import axios from 'axios';
import { invalidatedAll, receivedAll, requestedAll } from './actions';
import { fetchedAll } from './selects';
import type { PostedBulletpointType } from './types';
import {receivedUpdateSingle, requestedUpdateSingle} from "../bulletpoint/actions";

export const all = (theme: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(theme, getState())) return;
  dispatch(requestedAll(theme));
  axios.get(`/themes/${theme}/contributed_bulletpoints`)
    .then(response => dispatch(receivedAll(theme, response.data)));
};

export const add = (
  theme: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.post(`/themes/${theme}/contributed_bulletpoints`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const edit = (
  theme: number,
  id: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.put(`/contributed_bulletpoints/${id}`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const deleteOne = (
  theme: number,
  bulletpoint: number,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.delete(`/contributed_bulletpoints/${bulletpoint}`)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const updateSingle = (
  theme: number,
  bulletpoint: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(theme, bulletpoint);
  axios.get(`/contributed_bulletpoints/${bulletpoint}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};
