// @flow
import type { PaginationType } from './components/PaginationType';

export const getSourcePagination = (
  source: string,
  state: Object,
): PaginationType => (
  state.dataset[source] ? state.dataset[source].pagination : { page: 1, perPage: 10 }
);
