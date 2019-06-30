// @flow
export const UPLOAD_AVATAR = 'UPLOAD_AVATAR';

export const upload = (avatar: FormData, next) => ({
  type: UPLOAD_AVATAR,
  avatar,
  next,
});
