// @flow
import axios from 'axios';
import { receivedUpdateSingle, requestedUpdateSingle } from '../theme/bulletpoint/actions';

export const updateSingle = (
  theme: number,
  bulletpoint: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(theme, bulletpoint);
  axios.get(`/bulletpoints/${bulletpoint}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};
