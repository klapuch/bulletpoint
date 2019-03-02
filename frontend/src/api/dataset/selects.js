// @flow
import type { PaginationType } from './PaginationType';

export const getSourcePagination = (
  source: string,
  state: Object,
): ?PaginationType => (
  state.dataset[source] ? state.dataset[source].pagination : null
);
