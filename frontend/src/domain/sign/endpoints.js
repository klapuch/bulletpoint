// @flow
import * as tokens from '../token/endpoints';
import * as session from '../access/session';
import { requestedSignIn, requestedSignOut } from './actions';
import type { PostedCredentialsType, ProviderTypes } from './types';
import { fetchMe } from '../user/endpoints';
import { FACEBOOK_PROVIDER, GOOGLE_PROVIDER } from './types';

export const signIn = (
  provider: ProviderTypes,
  credentials: PostedCredentialsType,
) => (dispatch: (mixed) => Object) => {
  const onCreatedToken = login => Promise.resolve()
    .then(() => dispatch(requestedSignIn()))
    .then(() => fetchMe(login.token))
    .then(me => session.start({ expiration: login.expiration, value: login.token }, me));

  const onRefusedToken = error => Promise.reject(error);

  if (provider === FACEBOOK_PROVIDER) {
    return tokens.create({ login: credentials.login }, 'facebook')
      .then(onCreatedToken)
      .catch(onRefusedToken);
  } else if (provider === GOOGLE_PROVIDER) {
    return tokens.create({ login: credentials.login }, 'google')
      .then(onCreatedToken)
      .catch(onRefusedToken);
  } else {
    return tokens.create(credentials, null)
      .then(onCreatedToken)
      .catch(onRefusedToken);
  }
};

export const signOut = () => (dispatch: (mixed) => Object) => (
  tokens.invalidate()
    .finally(() => dispatch(requestedSignOut()))
    .then(session.destroy)
);

export const reSignIn = (token: ?string) => {
  const onCreatedToken = login => Promise.resolve()
    .then(() => fetchMe(login.token))
    .then(me => session.start({ expiration: login.expiration, value: login.token }, me));
  return tokens.refresh(token).then(onCreatedToken);
};
