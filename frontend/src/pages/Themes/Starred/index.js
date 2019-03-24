// @flow
import React from 'react';
import { connect } from 'react-redux';
import * as theme from '../../../domain/theme/endpoints';
import * as themes from '../../../domain/theme/selects';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';
import type { PaginationType } from '../../../api/dataset/PaginationType';
import { receivedInit, receivedReset, turnPage } from '../../../api/dataset/actions';
import { getSourcePagination } from '../../../api/dataset/selects';
import Pager from '../../../components/Pager';

type Props = {|
  +themes: Array<FetchedThemeType>,
  +total: number,
  +pagination: PaginationType,
  +fetching: boolean,
  +fetchStarred: (PaginationType) => (void),
  +initPaging: (PaginationType) => (void),
  +resetPaging: (PaginationType) => (void),
  +turnPage: (number, PaginationType) => (void),
|};
class StarredThemes extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  handleChangePage = (page: number) => {
    Promise.resolve()
      .then(() => this.props.turnPage(page, this.props.pagination))
      .then(() => this.reload());
  };

  reload(onResetPaging?: (PaginationType) => (void)) {
    const PER_PAGE = 5;
    Promise.resolve()
      .then(() => (typeof onResetPaging === 'undefined'
        ? this.props.initPaging
        : this.props.resetPaging))
      .then(initPaging => initPaging({ page: 1, perPage: PER_PAGE }))
      .then(() => this.props.fetchStarred(this.props.pagination));
  }

  render() {
    const { themes, fetching, total } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <h1>Oblíbená témata</h1>
        {total === 0 ? (
          <h2><small>Žádná oblíbená témata</small></h2>
        ) : (
          <>
            <Previews themes={themes} />
            <Pager
              total={total}
              pagination={this.props.pagination}
              onPageChange={this.handleChangePage}
            />
          </>
        )}
      </>
    );
  }
}

const SOURCE_NAME = 'themes/starred';
const mapStateToProps = state => ({
  total: themes.getTotal(state),
  themes: themes.getAll(state),
  fetching: themes.allFetching(state),
  pagination: getSourcePagination(SOURCE_NAME, state),
});
const mapDispatchToProps = dispatch => ({
  fetchStarred: (pagination: PaginationType) => dispatch(theme.fetchStarred(pagination)),
  initPaging: (
    paging: PaginationType,
  ) => dispatch(receivedInit(SOURCE_NAME, paging)),
  resetPaging: (
    paging: PaginationType,
  ) => dispatch(receivedReset(SOURCE_NAME, paging)),
  turnPage: (
    page: number,
    current: PaginationType,
  ) => dispatch(turnPage(SOURCE_NAME, page, current)),
});
export default connect(mapStateToProps, mapDispatchToProps)(StarredThemes);
