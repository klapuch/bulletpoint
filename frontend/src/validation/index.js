// @flow
import * as emailValidator from 'email-validator';
import * as urlValidator from 'valid-url';
import { flatten } from 'flat';
import { trim, first, isFunction } from 'lodash';

type Error = ?string;

export const REQUIRED = 'REQUIRED';
export const NOT_EMAIL = 'NOT_EMAIL';
export const NOT_USERNAME = 'NOT_USERNAME';
export const NOT_URL = 'NOT_URL';
export const MIN_6_CHARS = 'MIN_6_CHARS';
export const MAX_CHARS = 'MAX_CHARS';
export const MAX_ITEMS = 'MAX_ITEMS';

export const required = (value: ?mixed): Error => (
  value === null || trim(value).length === 0 ? REQUIRED : null
);

export const email = (value: ?string): Error => {
  if (required(value)) return required(value);
  else if (!emailValidator.validate(value)) return NOT_EMAIL;
  return null;
};

export const username = (value: string | null): Error => {
  if (required(value)) return required(value);
  else if (value !== null && !/^[a-zA-Z0-9_]{3,25}$/.test(value)) return NOT_USERNAME;
  return null;
};

export const url = (value: ?string): Error => {
  if (required(value)) {
    return required(value);
    // $FlowFixMe check is made via required
  } else if (!urlValidator.isWebUri(value) && !urlValidator.isWebUri(encodeURI(value))) {
    return NOT_URL;
  }
  return null;
};

export const password = (value: ?string): Error => {
  if (required(value)) return required(value);
  else if (trim(value).length < 6) return MIN_6_CHARS;
  return null;
};

export const maxChars = (value: ?string, chars: number): Error => (
  trim(value).length >= chars ? MAX_CHARS : null
);

export const maxItems = (value: Array<*>, items: number): Error => (
  value.length >= items ? MAX_ITEMS : null
);

export const requiredItems = (value: Array<*>): Error => (
  value.length === 0 ? REQUIRED : null
);

export const anyErrors = (validations: Object): boolean => (
  Object.values(flatten(validations)).filter(validation => validation).length > 0
);

export const firstError = (validations: Array<() => Error>): Error => {
  const error = first(validations.filter(validation => validation() !== null));
  if (isFunction(error)) {
    return error();
  }
  return null;
};
