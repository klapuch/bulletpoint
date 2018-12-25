// @flow
import axios from 'axios';
import { updateSingle } from '../../../theme/bulletpoint/endpoints';
import type { PointType } from '../types';

export const rate = (
  theme: number,
  bulletpoint: number,
  point: PointType,
) => (dispatch: (mixed) => Object) => {
  axios.post(`/bulletpoints/${bulletpoint}/ratings`, { point })
    .then(() => dispatch(updateSingle(theme, bulletpoint)));
};
