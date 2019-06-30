// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import * as theme from '../../domain/theme/actions';
import * as themes from '../../domain/theme/selects';
import type { FetchedThemeType } from '../../domain/theme/types';
import Previews from '../../domain/theme/components/Previews';
import FakePreviews from '../../domain/theme/components/FakePreviews';
import type { PaginationType } from '../../api/dataset/types';
import ActivePager from '../../api/dataset/components/Paging/ActivePager';
import { getSourcePagination } from '../../api/dataset/selects';

type Props = {|
  +themes: Array<FetchedThemeType>,
  +total: number,
  +pagination: PaginationType,
  +fetching: boolean,
  +fetchRecentThemes: (PaginationType) => (void),
  +pagination: PaginationType,
|};

const PER_PAGE = 5;
const PAGINATION_NAME = 'themes/';

class Themes extends React.Component<Props> {
  componentDidMount(): void {
    this.handleReload(this.props.pagination);
  }

  handleReload = (pagination: PaginationType) => this.props.fetchRecentThemes(pagination);

  render() {
    const { themes, fetching, total } = this.props;
    return (
      <>
        <Helmet>
          <title>Nedávno přidaná témata</title>
        </Helmet>
        <h1>Nedávno přidaná témata</h1>
        {fetching ? <FakePreviews>{PER_PAGE}</FakePreviews> : (
          <>
            <Previews tagLink={(id, slug) => `/themes/tag/${id}/${slug}`} themes={themes} />
            <ActivePager
              perPage={PER_PAGE}
              name={PAGINATION_NAME}
              total={total}
              onReload={this.handleReload}
            />
          </>
        )}
      </>
    );
  }
}

const mapStateToProps = state => ({
  total: themes.getTotal(state),
  themes: themes.getAll(state),
  fetching: themes.isAllFetching(state),
  pagination: getSourcePagination(PAGINATION_NAME, { page: 1, perPage: PER_PAGE }, state),
});
const mapDispatchToProps = dispatch => ({
  fetchRecentThemes: (pagination: PaginationType) => dispatch(theme.fetchRecent(pagination)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
