// @flow
import React from 'react';
import classNames from 'classnames';
import styled from 'styled-components';
import type { PaginationType } from '../api/dataset/PaginationType';

const MoveButton = styled.a`
  cursor: pointer;
`;

type Props = {|
  pagination: PaginationType,
  total: number,
  onPageChange: (number) => (void),
|};
const Pager = ({
  pagination,
  total,
  onPageChange,
}: Props) => {
  const lastPage = Math.ceil(total / pagination.perPage);
  const previousPage = Math.max(1, pagination.page - 1);
  const nextPage = Math.min(pagination.page + 1, lastPage);
  const isFirstPage = pagination.page === 1;
  const isLastPage = pagination.page === lastPage;

  const handlePageChange = (event, page) => {
    event.preventDefault();
    onPageChange(page);
  };

  if (isFirstPage && isLastPage) {
    return null;
  }

  return (
    <nav aria-label="Page navigation">
      <ul className="pager">
        <li className={classNames('previous', isFirstPage ? 'disabled' : null)}>
          <MoveButton onClick={isFirstPage ? () => null : e => handlePageChange(e, previousPage)}>
            <span aria-hidden="true">&larr;</span>
            {' '}
Previous
          </MoveButton>
        </li>
        <li className={classNames('next', isLastPage ? 'disabled' : null)}>
          <MoveButton onClick={isLastPage ? () => null : e => handlePageChange(e, nextPage)}>
            <span aria-hidden="true">&rarr;</span>
            {' '}
Next
          </MoveButton>
        </li>
      </ul>
    </nav>
  );
};

export default Pager;
