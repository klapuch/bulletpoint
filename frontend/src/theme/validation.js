// @flow
import memoize from 'memoizee';
import * as validation from '../validation';
import type { PostedThemeType, ErrorThemeType } from './types';

const UNKNOWN = 'UNKNOWN';

type FieldType = 'name' | 'tags' | 'reference_url';

const MAX_THEME_CHARS = 255;
const MAX_TAGS = 4;

export const errors = memoize((theme: PostedThemeType): ErrorThemeType => ({
  name: validation.firstError([
    () => validation.required(theme.name),
    () => validation.maxChars(theme.name, MAX_THEME_CHARS),
  ]),
  tags: validation.firstError([
    () => validation.requiredItems(theme.tags),
    () => validation.maxItems(theme.tags, MAX_TAGS),
  ]),
  reference_url: validation.firstError([
    () => validation.required(theme.reference.url),
    () => validation.url(theme.reference.url),
  ]),
}));

export const anyErrors = (theme: PostedThemeType): boolean => (
  validation.anyErrors(errors(theme))
);

export const toMessage = (errors: ErrorThemeType, field: FieldType) => ({
  name: {
    REQUIRED: 'Název je povinný',
    MAX_CHARS: `Maximální délka je ${MAX_THEME_CHARS} znaků`,
    UNKNOWN: 'Název není platný',
  },
  tags: {
    REQUIRED: 'Vyber aspoň 1 tag',
    MAX_ITEMS: `Vyber nejvíce ${MAX_TAGS} tagy`,
    UNKNOWN: 'Tagy nejsou platné',
  },
  reference_url: {
    REQUIRED: 'Uveď zdrojovou URL adresu',
    NOT_URL: 'Uveď platnou URL adresu',
    UNKNOWN: 'Odkaz není platný',
  },
}[field][errors[field] || UNKNOWN]);
