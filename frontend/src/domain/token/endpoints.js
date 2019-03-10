// @flow
import axios from 'axios';
import * as message from '../../ui/message/actions';
import type { PostedCredentialsType } from '../sign/types';

export const create = (
  credentials: PostedCredentialsType,
  next: (Object) => Promise<any>,
) => (dispatch: (mixed) => Object) => (
  axios.post('/tokens', credentials)
    .then(response => response.data)
    .then(next)
    .catch(error => dispatch(message.receivedApiError(error)))
);

export const createFacebook = (
  credentials: PostedProviderCredentialsType,
  next: (Object) => Promise<any>,
) => (dispatch: (mixed) => Object) => (
  axios.post('/facebook_tokens', credentials)
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
