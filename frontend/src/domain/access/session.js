// @flow
import { serialize, parse } from 'cookie';
import type { MeType } from '../user/types';

const VALUE_NAME = 'sessid';
const TTL_NAME = 'sessttl';
const ME_NAME = 'me';
const THRESHOLD = 10;
const DECREASED_BY = 40; // percent

type Token = {|
  +value: string,
  +expiration: number,
|};

export const start = (token: Token, me: MeType): void => {
  const idOptions = {
    path: '/',
    sameSite: 'lax',
  };
  const ttlOptions = {
    ...idOptions,
    maxAge: (token.expiration - THRESHOLD) * (DECREASED_BY / 100),
  };
  const meOptions = {
    ...idOptions,
  };
  window.document.cookie = serialize(VALUE_NAME, token.value, idOptions);
  window.document.cookie = serialize(TTL_NAME, 1, ttlOptions);
  window.document.cookie = serialize(ME_NAME, btoa(JSON.stringify(me)), meOptions);
};

export const destroy = (): void => {
  const options = {
    maxAge: -1,
    path: '/',
  };
  window.document.cookie = serialize(VALUE_NAME, '', options);
  window.document.cookie = serialize(TTL_NAME, '', options);
  window.document.cookie = serialize(ME_NAME, '', options);
};

export const getValue = (): ?string => {
  const cookie = parse(window.document.cookie);
  return !cookie || !cookie[VALUE_NAME] ? null : cookie[VALUE_NAME];
};

export const getTtl = (): ?number => {
  const cookie = parse(window.document.cookie);
  return !cookie || !cookie[TTL_NAME] ? null : cookie[TTL_NAME];
};

export const getMe = (): MeType => {
  const cookie = parse(window.document.cookie);
  return !cookie || !cookie[ME_NAME] ? { username: '', email: '', role: 'guest' } : JSON.parse(atob(cookie[ME_NAME]));
};

export const exists = (): boolean => getValue() !== null;

export const expired = (): boolean => getTtl() === null;
