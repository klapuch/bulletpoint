// @flow
import memoize from 'memoizee';
import * as validation from '../../validation';
import type { PostedTagType, ErrorTagType } from './types';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'name';

const MAX_NAME_CHARS = 255;

export const errors = memoize((tag: PostedTagType): ErrorTagType => ({
  name: validation.firstError([
    () => validation.required(tag.name),
    () => validation.maxChars(tag.name, MAX_NAME_CHARS),
  ]),
}));

export const anyErrors = (tag: PostedTagType): boolean => (
  validation.anyErrors(errors(tag))
);

export const toMessage = (errors: ErrorTagType, field: FieldType) => ({
  name: {
    REQUIRED: 'Název je povinný',
    MAX_CHARS: `Maximální délka je ${MAX_NAME_CHARS} znaků`,
    UNKNOWN: 'Název není platný',
  },
}[field][errors[field] || UNKNOWN]);

export const initErrors = {
  name: null,
};
