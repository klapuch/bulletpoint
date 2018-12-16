// @flow
import * as tokens from '../token/endpoints';
import * as session from '../access/session';
import { requestedSignIn, requestedSignOut } from './actions';
import * as message from '../ui/actions';

export type PostedCredentialsType = {|
  +email: string,
  +password: string,
|};

export const signIn = (
  credentials: PostedCredentialsType,
  next: (void) => void,
) => (dispatch: (mixed) => Object) => {
  dispatch(tokens.create(credentials, data => Promise.resolve()
    .then(() => dispatch(requestedSignIn()))
    .then(() => dispatch(message.receivedSuccess('Byl jsi přihlášen.')))
    .then(() => session.start({ expiration: data.expiration, value: data.token }))
    .then(next)));
};

export const signOut = (next: (void) => void) => (dispatch: (mixed) => Object) => {
  tokens.invalidate(() => Promise.resolve()
    .then(() => dispatch(requestedSignOut()))
    .then(session.destroy)
    .then(() => dispatch(message.receivedSuccess('Byl jsi odhlášen.')))
    .then(next));
};
