// @flow
import type { PaginationType } from './types';

export const getSourcePagination = (
  source: string,
  init: PaginationType,
  state: Object,
): PaginationType => (
  state.dataset[source] ? state.dataset[source].pagination : init
);
