// @flow
import React from 'react';
import { connect } from 'react-redux';
import Pager from './Pager';
import type { PaginationType } from '../../types';
import { receivedInit, receivedReset, turnPage } from '../../actions';
import { getSourcePagination } from '../../selects';

type Props = {|
  +reset?: boolean,
  +total: number,
  +onReload: (PaginationType) => (Promise<any>),
  +initPaging: (PaginationType) => (void),
  +resetPaging: (PaginationType) => (void),
  +turnPage: (number, PaginationType) => (void),
  +pagination: PaginationType,
  +perPage: number,
|};
class ActivePager extends React.Component<Props> {
  componentDidMount(): void {
    const { reset = false, perPage, pagination } = this.props;
    if (reset) {
      this.props.resetPaging({ page: 1, perPage });
    } else if (pagination.page === 1) {
      this.props.initPaging({ page: 1, perPage });
    }
  }

  handleChangePage = (page: number) => Promise.resolve()
    .then(() => this.props.turnPage(page, this.props.pagination))
    .then(() => this.props.onReload(this.props.pagination));

  render() {
    const { total, pagination } = this.props;
    return (
      <Pager
        total={total}
        pagination={pagination}
        onPageChange={this.handleChangePage}
      />
    );
  }
}

const mapStateToProps = (state, { name, perPage }) => ({
  pagination: getSourcePagination(name, { page: 1, perPage }, state),
});
const mapDispatchToProps = (dispatch, { name }) => ({
  turnPage: (
    page: number,
    current: PaginationType,
  ) => dispatch(turnPage(name, page, current)),
  initPaging: (
    paging: PaginationType,
  ) => dispatch(receivedInit(name, paging)),
  resetPaging: (
    paging: PaginationType,
  ) => dispatch(receivedReset(name, paging)),
});
export default connect(mapStateToProps, mapDispatchToProps)(ActivePager);
