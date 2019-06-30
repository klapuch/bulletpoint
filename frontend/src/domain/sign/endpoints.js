// @flow
import { call, put } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import * as tokens from '../token/endpoints';
import * as session from '../access/session';
import { fetchMe } from '../user/endpoints';
import { FACEBOOK_PROVIDER, GOOGLE_PROVIDER } from './types';
import { receivedSuccess, receivedApiError } from '../../ui/message/actions';

export function* signIn(action: Object): Saga {
  function* onCreatedToken(login): Saga {
    const me = yield call(fetchMe, login.token);
    yield call(session.start, { expiration: login.expiration, value: login.token }, me);
    yield put(receivedSuccess('Jsi úspěšně přihlášen.'));
    yield call(action.next);
  }

  try {
    if (action.provider === FACEBOOK_PROVIDER) {
      const login = yield call(tokens.create, { login: action.credentials.login }, 'facebook');
      yield call(onCreatedToken, login);
    } else if (action.provider === GOOGLE_PROVIDER) {
      const login = yield call(tokens.create, { login: action.credentials.login }, 'google');
      yield call(onCreatedToken, login);
    } else {
      const login = yield call(tokens.create, { login: action.credentials.login }, null);
      yield call(onCreatedToken, login);
    }
  } catch (error) {
    yield put(receivedApiError(error));
  }
}

export function* signOut(action: Object): Saga {
  try {
    yield call(tokens.invalidate);
  } finally {
    yield call(session.destroy);
    yield call(action.next);
  }
}

export const reSignIn = (token: ?string) => {
  const onCreatedToken = login => Promise.resolve()
    .then(() => fetchMe(login.token))
    .then(me => session.start({ expiration: login.expiration, value: login.token }, me));
  return tokens.refresh(token).then(onCreatedToken);
};
