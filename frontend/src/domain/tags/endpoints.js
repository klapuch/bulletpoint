// @flow
import axios from 'axios';
import { receivedAll, requestedAll } from './actions';
import { fetchedAll } from './selects';
import type {PostedTagType} from "./types";

export const all = () => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(getState())) return;
  dispatch(requestedAll());
  axios.get('tags')
    .then(response => dispatch(receivedAll(response.data)));
};

export const add = (
  tag: PostedTagType,
  next: (void) => (void),
) => (
  axios.post('/tags', tag).then(next)
);