// @flow
import * as tokens from '../token/endpoints';
import * as session from '../access/session';
import { requestedSignIn, requestedSignOut } from './actions';
import * as message from '../../ui/message/actions';
import type { PostedCredentialsType, ProviderType } from './types';
import { fetchMe } from '../user/endpoints';
import type { MeType } from '../user/types';
import {FACEBOOK_PROVIDER, INTERNAL_PROVIDER} from "./types";

export const signIn = (
  provider: ProviderType,
  credentials: PostedCredentialsType,
  next: (void) => void,
) => (dispatch: (mixed) => Object) => {
  const onReceivedUser = login => fetchMe(
    login.token,
    (me: MeType) => Promise.resolve()
      .then(() => session.start({ expiration: login.expiration, value: login.token }, me))
      .then(() => dispatch(message.receivedSuccess('Jsi přihlášen.')))
      .then(next),
  );

  const onCreatedToken = data => Promise.resolve()
    .then(() => dispatch(requestedSignIn()))
    .then(() => onReceivedUser(data));

  if (provider === FACEBOOK_PROVIDER) {
    dispatch(tokens.createFacebook({ login: credentials.login }, onCreatedToken));
  } else if (provider === INTERNAL_PROVIDER) {
    dispatch(tokens.create(credentials, onCreatedToken));
  }
};

export const signOut = (next: (void) => void) => (dispatch: (mixed) => Object) => {
  tokens.invalidate(() => Promise.resolve()
    .then(() => dispatch(requestedSignOut()))
    .then(session.destroy)
    .then(() => dispatch(message.receivedSuccess('Byl jsi odhlášen.')))
    .then(next));
};

export const reSignIn = (
  token: ?string,
  error: () => (void),
) => {
  tokens.refresh(
    token,
    login => fetchMe(
      login.token,
      (me: MeType) => session.start({ expiration: login.expiration, value: login.token }, me),
    ),
    () => {
      session.destroy();
      error();
    },
  );
};
