// @flow
import axios from 'axios';
import {
  requestedSingle,
  receivedSingle,
  requestedAll,
  receivedAll,
} from './actions';
import { fetchedSingle } from './selects';
import * as response from '../api/response';
import type { TagType } from '../tags/endpoints';

export type FetchedThemeType = {|
  +id: number,
  +user_id: number,
  +tags: Array<TagType>,
  +name: string,
  +created_at: string,
  +reference: {|
    +url: string,
  |}
|};

export type PostedThemeType = {|
  +tags: Array<number>,
  +name: string,
  +reference: {|
    +url: string,
  |}
|};

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
