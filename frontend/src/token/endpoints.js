// @flow
import axios from 'axios';
import type { PostedCredentialsType } from '../sign/endpoints';

export const create = (
  Credentials: PostedCredentialsType,
  next: (Object) => Promise<any>,
) => (dispatch: (mixed) => Object) => ( // eslint-disable-line
  axios.post('/tokens', Credentials)
    .then(response => response.data)
    .then(next)
);

export const invalidate = (next: (void) => Promise<any>) => (
  axios.delete('/tokens').finally(next)
);

export const refresh = (
  token: ?string,
  next: (Object) => (void),
  error: () => (void),
) => (
  axios.post('/refresh_tokens', { token })
    .then(response => response.data)
    .then(next)
    .catch(error)
);
