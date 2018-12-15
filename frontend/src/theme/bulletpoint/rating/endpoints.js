// @flow
import axios from 'axios';
import { invalidatedAll } from '../actions';

export const rate = (
  theme: number,
  bulletpoint: number,
  point: number,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.post(`/bulletpoints/${bulletpoint}/ratings`, { point })
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};
