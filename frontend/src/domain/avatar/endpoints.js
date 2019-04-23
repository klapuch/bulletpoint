// @flow
import axios from 'axios';

export const upload = (avatar: FormData) => (
  axios.post('/avatars', avatar)
    .catch(error => Promise.reject(error.response.data.message))
);
