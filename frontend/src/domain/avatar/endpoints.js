// @flow
import axios from 'axios';

export const upload = (avatar: FormData) => (
  axios.post('/avatars', avatar)
);
