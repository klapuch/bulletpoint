// @flow
import axios from 'axios';
import { updateSingle } from '../../../bulletpoint/endpoints';

export const rate = (
  theme: number,
  bulletpoint: number,
  point: number,
) => (dispatch: (mixed) => Object) => {
  axios.post(`/bulletpoints/${bulletpoint}/ratings`, { point })
    .then(() => dispatch(updateSingle(theme, bulletpoint)));
};
