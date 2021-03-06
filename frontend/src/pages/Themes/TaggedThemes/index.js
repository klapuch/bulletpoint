// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import * as theme from '../../../domain/theme/actions';
import * as themes from '../../../domain/theme/selects';
import SlugRedirect from '../../../router/SlugRedirect';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Error from '../../../ui/Error';
import Previews from '../../../domain/theme/components/Previews';
import type { PaginationType } from '../../../api/dataset/types';
import ActivePager from '../../../api/dataset/components/Paging/ActivePager';
import { getSourcePagination } from '../../../api/dataset/selects';
import SkeletonPreviews from '../../../domain/theme/components/SkeletonPreviews';

type Props = {|
  +match: {|
    +params: {|
      +tag: string,
    |},
  |},
  +themes: Array<FetchedThemeType>,
  +total: number,
  +fetching: boolean,
  +fetchTaggedThemes: (PaginationType, next: () => void) => (void),
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
      this.handleReload({ page: 1, perPage: PER_PAGE }, () => this.setState({ reset: true }));
    }
  }

  getTag = () => {
    const { match: { params: { tag } } } = this.props;
    return themes.getCommonTag(this.props.themes, parseInt(tag, 10));
  };

  handleReload = (pagination: PaginationType, next: () => void = () => {}) => (
    this.props.fetchTaggedThemes(
      pagination,
      next,
    ));

  render() {
    const { themes, fetching, total } = this.props;
    if (fetching) {
      return (
        <>
          <h1>Témata vybraná pro &quot;<strong>...</strong>&quot;</h1>
          <SkeletonPreviews>{PER_PAGE}</SkeletonPreviews>
        </>
      );
    }
    const tag = this.getTag();
    if (tag === undefined) {
      return <Error>Tag neexistuje.</Error>;
    }
    return (
      <SlugRedirect {...this.props} name={tag}>
        <Helmet>
          <title>{`Témata vybraná pro ${tag}`}</title>
        </Helmet>
        <h1>Témata vybraná pro &quot;<strong>{tag}</strong>&quot;</h1>
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
    next: () => void,
  ) => dispatch(theme.fetchByTag(parseInt(tag, 10), pagination), next),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
