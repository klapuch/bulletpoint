// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import { isEmpty } from 'lodash';
import * as theme from '../../domain/theme/endpoints';
import * as themes from '../../domain/theme/selects';
import Loader from '../../ui/Loader';
import SlugRedirect from '../../router/SlugRedirect';
import type { FetchedThemeType } from '../../domain/theme/types';
import Previews from '../../domain/theme/components/Previews';
import type { PaginationType } from '../../api/dataset/components/PaginationType';
import ActivePager from '../../api/dataset/components/ActivePager';
import { getSourcePagination } from '../../api/dataset/selects';

type Props = {|
  +params: {|
    +tag: ?number,
  |},
  +match: {|
    +params: {|
      +tag: ?string,
    |},
  |},
  +themes: Array<FetchedThemeType>,
  +total: number,
  +pagination: PaginationType,
  +fetching: boolean,
  +fetchRecentThemes: (PaginationType) => (void),
  +fetchTaggedThemes: (tag: number, PaginationType) => (void),
  +initPaging: (PaginationType) => (void),
  +resetPaging: (PaginationType) => (void),
  +turnPage: (number, PaginationType) => (void),
  +pagination: PaginationType,
|};
type State = {|
  reset: boolean,
|};

const PER_PAGE = 5;
const PAGINATION_NAME = 'themes/';

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

  getHeader = () => {
    if (isEmpty(this.props.match.params.tag)) {
      return 'Nedávno přidaná témata';
    }
    return <>Témata vybraná pro &quot;<strong>{this.getTag()}</strong>&quot;</>;
  };

  getTitle = () => {
    if (isEmpty(this.props.match.params.tag)) {
      return 'Nedávno přidaná témata';
    }
    return `Témata vybraná pro "${this.getTag()}"`;
  };

  getTag = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return '';
    }
    return themes.getCommonTag(this.props.themes, parseInt(tag, 10));
  };

  handleReload = (pagination: PaginationType) => Promise.resolve()
    .then(() => {
      const { match: { params: { tag } } } = this.props;
      if (isEmpty(tag)) {
        this.props.fetchRecentThemes(pagination);
      } else {
        this.props.fetchTaggedThemes(parseInt(tag, 10), pagination);
      }
    });

  render() {
    const { themes, fetching, total } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.getTag()}>
        <Helmet>
          <title>{this.getTitle()}</title>
        </Helmet>
        <h1>{this.getHeader()}</h1>
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
const mapDispatchToProps = dispatch => ({
  fetchRecentThemes: (pagination: PaginationType) => dispatch(theme.fetchRecent(pagination)),
  fetchTaggedThemes: (
    tag: number,
    pagination: PaginationType,
  ) => dispatch(theme.fetchByTag(tag, pagination)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
