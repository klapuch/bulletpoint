// @flow
import * as tokens from '../token/endpoints';
import * as session from '../access/session';

export type PostedCredentialsType = {|
  +email: string,
  +password: string,
|};

export const signIn = (
  credentials: PostedCredentialsType,
  next: (void) => void,
) => (dispatch: (mixed) => Object) => {
  dispatch(tokens.create(credentials, data => Promise.resolve()
    .then(() => session.start({ expiration: data.expiration, value: data.token }))
    .then(next)));
};

export const signOut = (next: (void) => void) => {
  tokens.invalidate(() => Promise.resolve()
    .then(session.destroy)
    .then(next));
};
