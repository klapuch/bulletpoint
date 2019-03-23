// @flow
import axios from 'axios';

export const upload = (avatar: File) => (
  axios.post('/avatars', avatar)
);
