// @flow
import memoize from 'memoizee';
import * as validation from '../validation';
import type { PostedCredentialsType, ErrorCredentialsType } from './types';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'login'|'password';

export const errors = memoize((credentials: PostedCredentialsType): ErrorCredentialsType => ({
  login: credentials.login.includes('@')
    ? validation.email(credentials.login)
    : validation.username(credentials.login),
  password: validation.password(credentials.password),
}));

export const anyErrors = (credentials: PostedCredentialsType): boolean => (
  validation.anyErrors(errors(credentials))
);

export const toMessage = (errors: ErrorCredentialsType, field: FieldType) => ({
  login: {
    REQUIRED: 'Uživatelské jméno nebo email je povinný',
    NOT_EMAIL: 'E-mail není platný',
    NOT_USERNAME: 'Uživatelské jméno není platné',
    UNKNOWN: 'Uživatelské jméno nebo email není platný',
  },
  password: {
    REQUIRED: 'Heslo je povinné',
    MIN_6_CHARS: 'Špatné heslo',
    UNKNOWN: 'Heslo není platné',
  },
}[field][errors[field] || UNKNOWN]);
