// @flow
import axios from 'axios';
import * as message from '../ui/message/actions';
import type { PostedCredentialsType } from '../sign/types';

export const create = (
  Credentials: PostedCredentialsType,
  next: (Object) => Promise<any>,
) => (dispatch: (mixed) => Object) => (
  axios.post('/tokens', Credentials)
    .then(response => response.data)
    .then(next)
    .catch(error => dispatch(message.receivedApiError(error)))
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
