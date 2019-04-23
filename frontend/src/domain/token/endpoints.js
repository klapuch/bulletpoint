// @flow
import axios from 'axios';
import type { PostedCredentialsType, PostedProviderCredentialsType } from '../sign/types';

export const create = (
  credentials: PostedCredentialsType|PostedProviderCredentialsType,
  provider: string|null,
) => (
  axios.post('/tokens', credentials, { params: { provider } })
    .then(response => response.data)
    .catch(error => Promise.reject(error.response.data.message))
);

export const invalidate = () => (
  axios.delete('/tokens')
);

export const refresh = (token: ?string) => (
  axios.post('/refresh_tokens', { token })
    .then(response => response.data)
);
