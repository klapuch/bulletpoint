// @flow
import axios from 'axios';
import {
  requestedAll,
  receivedAll,
  invalidatedAll,
} from './actions';
import { fetchedAll } from './selects';

export type FetchedBulletpointType = {|
  +id: number,
  +source: {|
    +link: string,
    +type: string,
  |},
  +user_id: number,
  +rating: {|
    +up: number,
    +down: number,
    +total: number,
  |},
  +content: string,
  +theme_id: number,
|};

export type PostedBulletpointType = {|
  +source: {|
    +link: string,
    +type: string,
  |},
  +content: string,
|};

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
