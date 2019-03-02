// @flow
import type { PaginationType } from './PaginationType';

export const RECEIVED_PAGINATION = 'RECEIVED_PAGINATION';
export const RECEIVED_INIT_PAGING = 'RECEIVED_INIT_PAGING';

export const receivedPagination = (source: string, pagination: PaginationType) => ({
  type: RECEIVED_PAGINATION,
  source,
  pagination,
});

export const receivedInit = (source: string, pagination: PaginationType) => ({
  type: RECEIVED_INIT_PAGING,
  source,
  pagination,
});

export const turnPage = (source: string, page: number, current: PaginationType) => (
  receivedPagination(source, { page, perPage: current.perPage })
);

export const changePerPage = (source: string, perPage: number, current: PaginationType) => (
  receivedPagination(source, { perPage, page: current.page })
);
