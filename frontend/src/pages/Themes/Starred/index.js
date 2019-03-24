// @flow
import React from 'react';
import { connect } from 'react-redux';
import qs from 'qs';
import * as theme from '../../../domain/theme/endpoints';
import * as themes from '../../../domain/theme/selects';
import * as tags from '../../../domain/tags/selects';
import * as tag from '../../../domain/tags/endpoints';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';
import type { PaginationType } from '../../../api/dataset/PaginationType';
import { receivedInit, receivedReset, turnPage } from '../../../api/dataset/actions';
import { getSourcePagination } from '../../../api/dataset/selects';
import Pager from '../../../components/Pager';
import Labels from '../../../domain/tags/components/Labels';
import type { FetchedTagType } from '../../../domain/tags/types';

type Props = {|
  +location: {|
    +search: string,
  |},
  +tags: Array<FetchedTagType>,
  +themes: Array<FetchedThemeType>,
  +total: number,
  +pagination: PaginationType,
  +fetching: boolean,
  +fetchStarred: (PaginationType, ?number) => (void),
  +initPaging: (PaginationType) => (void),
  +resetPaging: (PaginationType) => (void),
  +turnPage: (number, PaginationType) => (void),
  +fetchTags: () => (void),
|};
class StarredThemes extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  componentDidUpdate(prevProps: Props) {
    const { location: { search } } = this.props;
    if (prevProps.location.search !== search) {
      this.reload(pagination => this.props.resetPaging(pagination));
    }
  }

  handleChangePage = (page: number) => {
    Promise.resolve()
      .then(() => this.props.turnPage(page, this.props.pagination))
      .then(() => this.reload());
  };

  getTagId = (): ?number => {
    const { location: { search } } = this.props;
    const { tag_id: tagId } = qs.parse(search, { ignoreQueryPrefix: true });
    if (typeof tagId === 'undefined') {
      return undefined;
    }
    return parseInt(tagId, 10);
  };

  reload(onResetPaging?: (PaginationType) => (void)) {
    const PER_PAGE = 5;
    Promise.resolve()
      .then(() => (typeof onResetPaging === 'undefined'
        ? this.props.initPaging
        : this.props.resetPaging))
      .then(initPaging => initPaging({ page: 1, perPage: PER_PAGE }))
      .then(() => this.props.fetchStarred(this.props.pagination, this.getTagId()))
      .then(() => this.props.fetchTags());
  }

  render() {
    const {
      themes,
      fetching,
      total,
      tags,
    } = this.props;
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
            {typeof this.getTagId() === 'undefined' && <Labels tags={tags} link={id => `?tag_id=${id}`} />}
            <Previews themes={themes} tagLink={id => `?tag_id=${id}`} />
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
  tags: tags.getStarred(state),
  fetching: themes.allFetching(state) || tags.starredFetching(state),
  pagination: getSourcePagination(SOURCE_NAME, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.fetchStarred()),
  fetchStarred: (
    pagination: PaginationType,
    tagId: ?number,
  ) => dispatch(theme.fetchStarred(pagination, tagId)),
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
