// @flow
import memoize from 'memoizee';
import * as validation from '../validation';
import type { PostedCredentialsType, ErrorCredentialsType } from './types';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'email'|'password';

export const errors = memoize((credentials: PostedCredentialsType): ErrorCredentialsType => ({
  email: validation.email(credentials.email),
  password: validation.password(credentials.password),
}));

export const anyErrors = (credentials: PostedCredentialsType): boolean => (
  validation.anyErrors(errors(credentials))
);

export const toMessage = (errors: ErrorCredentialsType, field: FieldType) => ({
  email: {
    REQUIRED: 'E-mail je povinný',
    NOT_EMAIL: 'E-mail není platný',
    UNKNOWN: 'Email není platný',
  },
  password: {
    REQUIRED: 'Heslo je povinné',
    MIN_6_CHARS: 'Špatné heslo',
    UNKNOWN: 'Heslo není platné',
  },
}[field][errors[field] || UNKNOWN]);
