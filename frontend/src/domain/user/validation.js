// @flow
import memoize from 'memoizee';
import { trim } from 'lodash';
import * as validation from '../../validation';
import type { PostedUserType, ErrorUserType } from './types';

const UNKNOWN = 'UNKNOWN';
const NO_SPACE = 'NO_SPACE';

type FieldType = 'username';

const MAX_USERNAME_LENGTH = 25;
const MIN_USERNAME_LENGTH = 3;

const noSpace = (value: string) => (trim(value) !== value ? NO_SPACE : null);

export const errors = memoize((user: PostedUserType): ErrorUserType => ({
  username: validation.firstError([
    () => validation.required(user.username),
    () => validation.maxChars(user.username, MAX_USERNAME_LENGTH),
    () => validation.minChars(user.username, MIN_USERNAME_LENGTH),
    () => noSpace(user.username),
  ]),
}));

export const anyErrors = (user: PostedUserType): boolean => (
  validation.anyErrors(errors(user))
);

export const toMessage = (errors: ErrorUserType, field: FieldType) => ({
  username: {
    REQUIRED: 'Uživatelské jméno je povinné',
    MAX_CHARS: `Maximální délka je ${MAX_USERNAME_LENGTH} znaků`,
    NO_SPACE: 'Uživatelské jméno nesmí obsahovat mezery.',
    MIN_CHARS: `Minimální délka jsou ${MIN_USERNAME_LENGTH} znaky`,
    UNKNOWN: 'Uživatelské jméno není platné',
  },
}[field][errors[field] || UNKNOWN]);
