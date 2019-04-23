// @flow
import axios from 'axios';
import {
  receivedAll,
  requestedAll,
  receivedStarred,
  requestedStarred,
} from './actions';
import { fetchedAll, fetchedStarred } from './selects';
import type { PostedTagType } from './types';

export const fetchAll = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(getState())) {
    return Promise.resolve();
  }
  dispatch(requestedAll());
  return axios.get('tags')
    .then(response => dispatch(receivedAll(response.data)));
};

export const fetchStarred = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedStarred(getState())) {
    return Promise.resolve();
  }
  dispatch(requestedStarred());
  return axios.get('starred_tags')
    .then(response => dispatch(receivedStarred(response.data)));
};

export const add = (tag: PostedTagType) => (
  axios.post('/tags', tag)
    .catch(error => Promise.reject(error.response.data.message))
);
