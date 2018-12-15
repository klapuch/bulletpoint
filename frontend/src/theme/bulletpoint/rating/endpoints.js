// @flow
import axios from 'axios';

export const rate = (
  theme: number,
  bulletpoint: number,
  point: number,
  next: (void) => (void),
) => () => {
  axios.post(`/bulletpoints/${bulletpoint}/ratings`, { point })
    .then(next);
};
