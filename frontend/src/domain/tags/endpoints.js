// @flow
import axios from 'axios';
import {
  receivedAll, requestedAll, receivedStarred, requestedStarred,
} from './actions';
import { fetchedAll, fetchedStarred } from './selects';
import type { PostedTagType } from './types';
import { receivedApiError } from '../../ui/message/actions';

export const fetchAll = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(getState())) return;
  dispatch(requestedAll());
  axios.get('tags')
    .then(response => dispatch(receivedAll(response.data)));
};

export const fetchStarred = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedStarred(getState())) return;
  dispatch(requestedStarred());
  axios.get('starred_tags')
    .then(response => dispatch(receivedStarred(response.data)));
};

export const add = (
  tag: PostedTagType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => (
  axios.post('/tags', tag)
    .then(next)
    .catch(error => dispatch(receivedApiError(error)))
);
