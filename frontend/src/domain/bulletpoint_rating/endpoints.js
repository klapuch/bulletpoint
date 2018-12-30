// @flow
import axios from 'axios';
import type { PointType } from './types';

export const rate = (
  bulletpoint: number,
  point: PointType,
  next: () => (void),
) => {
  axios.post(`/bulletpoints/${bulletpoint}/ratings`, { point })
    .then(next);
};
