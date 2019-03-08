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
import { default as AllThemes } from '../../domain/theme/components/Previews';
import type { PaginationType } from '../../api/dataset/PaginationType';
import { receivedInit, turnPage, receivedReset } from '../../api/dataset/actions';
import { getSourcePagination } from '../../api/dataset/selects';
import Pager from '../../components/Pager';

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
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { tag } } } = this.props;
    if (prevProps.match.params.tag !== tag) {
      this.reload(pagination => this.props.resetPaging(pagination));
    }
  }

  getHeader = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    return <>Témata vybraná pro &quot;<strong>{this.getTag()}</strong>&quot;</>;
  };

  getTitle = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
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

  handleChangePage = (page: number) => {
    Promise.resolve()
      .then(() => this.props.turnPage(page, this.props.pagination))
      .then(() => this.reload());
  };

  reload(onResetPaging?: (PaginationType) => (void)) {
    const PER_PAGE = 5;
    Promise.resolve()
      .then(() => {
        return typeof onResetPaging === 'undefined'
          ? this.props.initPaging
          : this.props.resetPaging;
      })
      .then(initPaging => initPaging({ page: 1, perPage: PER_PAGE }))
      .then(() => {
        const { match: { params: { tag } }, pagination } = this.props;
        if (isEmpty(tag)) {
          this.props.fetchRecentThemes(pagination);
        } else {
          this.props.fetchTaggedThemes(parseInt(tag, 10), pagination);
        }
      });
  }

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.getTag()}>
        <Helmet>
          <title>{this.getTitle()}</title>
        </Helmet>
        <h1>{this.getHeader()}</h1>
        <br />
        <AllThemes themes={themes} />
        <Pager
          total={this.props.total}
          pagination={this.props.pagination}
          onPageChange={this.handleChangePage}
        />
      </SlugRedirect>
    );
  }
}

const SOURCE_NAME = 'themes';
const mapStateToProps = state => ({
  total: themes.getTotal(state),
  themes: themes.getAll(state),
  fetching: themes.allFetching(state),
  pagination: getSourcePagination(SOURCE_NAME, state),
});
const mapDispatchToProps = dispatch => ({
  fetchRecentThemes: (pagination: PaginationType) => dispatch(theme.allRecent(pagination)),
  fetchTaggedThemes: (
    tag: number,
    pagination: PaginationType,
  ) => dispatch(theme.allByTag(tag, pagination)),
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
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
