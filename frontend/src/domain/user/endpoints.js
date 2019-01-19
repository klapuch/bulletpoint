// @flow

import axios from 'axios';
import type { MeType } from './types';

export const fetchMe = (token: string, next: (MeType) => (Promise<any>|void)) => (
  axios.get('/users/me', { headers: { Authorization: `Bearer ${token}` } })
    .then(response => response.data)
    .then(next)
);
