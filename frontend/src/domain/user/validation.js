// @flow
import memoize from 'memoizee';
import { trim } from 'lodash';
import * as validation from '../../validation';
import type { PostedUserType, ErrorUserType } from './types';

const UNKNOWN = 'UNKNOWN';
const NO_SPACE = 'NO_SPACE';
const NO_EXTRA_CHARS = 'NO_EXTRA_CHARS';

type FieldType = 'username';

const MAX_USERNAME_LENGTH = 25;
const MIN_USERNAME_LENGTH = 3;

const noSpace = (value: string) => (trim(value) === value ? null : NO_SPACE);
const noExtraChars = (value: string) => (/^[a-zA-Z0-9_]+$/.test(value) ? null : NO_EXTRA_CHARS);

export const errors = memoize((user: PostedUserType): ErrorUserType => ({
  username: validation.firstError([
    () => validation.required(user.username),
    () => validation.maxChars(user.username, MAX_USERNAME_LENGTH),
    () => validation.minChars(user.username, MIN_USERNAME_LENGTH),
    () => noSpace(user.username),
    () => noExtraChars(user.username),
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
    NO_EXTRA_CHARS: 'Uživatelské jméno smí obsahovat pouze základní znaky.',
    MIN_CHARS: `Minimální délka jsou ${MIN_USERNAME_LENGTH} znaky`,
    UNKNOWN: 'Uživatelské jméno není platné',
  },
}[field][errors[field] || UNKNOWN]);
