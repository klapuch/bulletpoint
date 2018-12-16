// @flow
import * as emailValidator from 'email-validator';
import { flatten } from 'flat';
import { trim } from 'lodash';

type Error = ?string;

export const REQUIRED = 'REQUIRED';
export const NOT_EMAIL = 'NOT_EMAIL';
export const MIN_6_CHARS = 'MIN_6_CHARS';

export const required = (value: ?mixed): Error => (
  value === null || trim(value).length === 0 ? REQUIRED : null
);

export const email = (value: ?string): Error => {
  if (required(value)) return required(value);
  else if (!emailValidator.validate(value)) return NOT_EMAIL;
  return null;
};

export const password = (value: ?string): Error => {
  if (required(value)) return required(value);
  else if (trim(value).length < 6) return MIN_6_CHARS;
  return null;
};

export const anyErrors = (validations: Object): boolean => (
  Object.values(flatten(validations)).filter(validation => validation).length > 0
);
