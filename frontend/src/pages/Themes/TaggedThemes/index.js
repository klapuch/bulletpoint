// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import * as theme from '../../../domain/theme/actions';
import * as themes from '../../../domain/theme/selects';
import Loader from '../../../ui/Loader';
import SlugRedirect from '../../../router/SlugRedirect';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';
import type { PaginationType } from '../../../api/dataset/types';
import ActivePager from '../../../api/dataset/components/Paging/ActivePager';
import { getSourcePagination } from '../../../api/dataset/selects';

type Props = {|
  +match: {|
    +params: {|
      +tag: string,
    |},
  |},
  +themes: Array<FetchedThemeType>,
  +total: number,
  +pagination: PaginationType,
  +fetching: boolean,
  +fetchTaggedThemes: (PaginationType) => (Promise<void>),
  +initPaging: (PaginationType) => (void),
  +resetPaging: (PaginationType) => (void),
  +turnPage: (number, PaginationType) => (void),
  +pagination: PaginationType,
|};
type State = {|
  reset: boolean,
|};

const PER_PAGE = 5;
const PAGINATION_NAME = 'themes/tags/';

class Themes extends React.Component<Props, State> {
  state = {
    reset: false,
  };

  componentDidMount(): void {
    this.handleReload(this.props.pagination);
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { tag } } } = this.props;
    if (prevProps.match.params.tag !== tag) {
      this.handleReload({ page: 1, perPage: PER_PAGE })
        .then(() => this.setState({ reset: true }));
    }
  }

  getTag = () => {
    const { match: { params: { tag } } } = this.props;
    return themes.getCommonTag(this.props.themes, parseInt(tag, 10));
  };

  handleReload = (pagination: PaginationType) => this.props.fetchTaggedThemes(pagination);

  render() {
    const { themes, fetching, total } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.getTag()}>
        <Helmet>
          <title>{`Témata vybraná pro ${this.getTag()}`}</title>
        </Helmet>
        <h1>Témata vybraná pro &quot;<strong>{this.getTag()}</strong>&quot;</h1>
        <Previews tagLink={(id, slug) => `/themes/tag/${id}/${slug}`} themes={themes} />
        <ActivePager
          perPage={PER_PAGE}
          name={PAGINATION_NAME}
          reset={this.state.reset}
          total={total}
          onReload={this.handleReload}
        />
      </SlugRedirect>
    );
  }
}

const mapStateToProps = state => ({
  total: themes.getTotal(state),
  themes: themes.getAll(state),
  fetching: themes.isAllFetching(state),
  pagination: getSourcePagination(PAGINATION_NAME, { page: 1, perPage: PER_PAGE }, state),
});
const mapDispatchToProps = (dispatch, { match: { params: { tag } } }) => ({
  fetchTaggedThemes: (
    pagination: PaginationType,
  ) => dispatch(theme.fetchByTag(parseInt(tag, 10), pagination)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
