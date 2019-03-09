// @flow
import memoize from 'memoizee';
import * as validation from '../../validation';
import type { PostedBulletpointType, ErrorBulletpointType } from './types';
import * as formats from './formats';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'content' | 'source_link' | 'source_type' | 'referenced_themes';

const MAX_BULLETPOINT_CHARS = 255;
const NOT_ENOUGH_REFERENCES = 'NOT_ENOUGH_REFERENCES';

const enoughReferences = (content: string, references: Array<number>) => (
  references.length === formats.numberOfReferences(content)
    ? null
    : NOT_ENOUGH_REFERENCES
);

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
  referenced_themes: validation.firstError([
    () => enoughReferences(bulletpoint.content, bulletpoint.referenced_theme_id),
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
  referenced_themes: {
    NOT_ENOUGH_REFERENCES: 'Počet odkazovaných témat musí sedět s odkazama v textu.',
    UNKNOWN: 'Odkazující se témata nejsou platné.',
  },
}[field][errors[field] || UNKNOWN]);

export const initErrors = {
  content: null,
  source_type: null,
  source_link: null,
  referenced_themes: null,
};
