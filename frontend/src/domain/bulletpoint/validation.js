// @flow
import memoize from 'memoizee';
import * as validation from '../../validation';
import type { PostedBulletpointType, ErrorBulletpointType } from './types';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'content' | 'source_link' | 'source_type';

const MAX_BULLETPOINT_CHARS = 255;

export const errors = memoize((bulletpoint: PostedBulletpointType): ErrorBulletpointType => ({
  content: validation.firstError([
    () => validation.required(bulletpoint.content),
    () => validation.maxChars(bulletpoint.content, MAX_BULLETPOINT_CHARS),
  ]),
  source_type: null, // select box
  source_link: bulletpoint.source.type === 'head' ? null : validation.firstError([
    () => validation.required(bulletpoint.source.link),
    () => validation.url(bulletpoint.source.link),
  ]),
}));

export const anyErrors = (bulletpoint: PostedBulletpointType): boolean => (
  validation.anyErrors(errors(bulletpoint))
);

export const toMessage = (errors: ErrorBulletpointType, field: FieldType) => ({
  content: {
    REQUIRED: 'Obsah je povinný',
    MAX_CHARS: `Maximální délka je ${MAX_BULLETPOINT_CHARS} znaků`,
    UNKNOWN: 'Obsah není platný',
  },
  source_type: {
    UNKNOWN: 'Typ není platný',
  },
  source_link: {
    REQUIRED: 'Uveď zdrojovou URL adresu',
    NOT_URL: 'Uveď platnou URL adresu',
    UNKNOWN: 'Zdrojová URL není platná',
  },
}[field][errors[field] || UNKNOWN]);
