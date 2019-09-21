// @flow
import React from 'react';
import { connect } from 'react-redux';
import qs from 'qs';
import * as theme from '../../../domain/theme/actions';
import * as themes from '../../../domain/theme/selects';
import * as tags from '../../../domain/tags/selects';
import * as tag from '../../../domain/tags/actions';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';
import type { PaginationType } from '../../../api/dataset/types';
import Labels from '../../../domain/tags/components/Labels';
import type { FetchedTagType } from '../../../domain/tags/types';
import ActivePager from '../../../api/dataset/components/Paging/ActivePager';
import { getSourcePagination } from '../../../api/dataset/selects';
import SkeletonPreviews from '../../../domain/theme/components/SkeletonPreviews';

type Props = {|
  +location: {|
    +search: string,
  |},
  +tags: Array<FetchedTagType>,
  +themes: Array<FetchedThemeType>,
  +total: number,
  +fetching: boolean,
  +fetchStarred: (PaginationType, ?number) => (void),
  +fetchTags: () => (void),
  +pagination: PaginationType,
|};
type State = {|
  reset: boolean,
|};

const PER_PAGE = 5;
const PAGINATION_NAME = 'themes/starred';

class StarredThemes extends React.Component<Props, State> {
  state = {
    reset: false,
  };

  componentDidMount(): void {
    this.reload(this.props.pagination);
  }

  componentDidUpdate(prevProps: Props) {
    const { location: { search } } = this.props;
    if (prevProps.location.search !== search) {
      // eslint-disable-next-line
      this.setState({ reset: true }, () => this.reload({ page: 1, perPage: PER_PAGE }));
    }
  }

  getTagId = (): ?number => {
    const { location: { search } } = this.props;
    const { tag_id: tagId } = qs.parse(search, { ignoreQueryPrefix: true });
    return tagId === undefined ? undefined : parseInt(tagId, 10);
  };

  reload = (pagination: PaginationType): Promise<any> => Promise.all([
    this.props.fetchStarred(pagination, this.getTagId()),
    this.props.fetchTags(),
  ]);

  render() {
    const {
      themes,
      fetching,
      total,
      tags,
    } = this.props;
    return (
      <>
        <h1>Oblíbená témata</h1>
        {!fetching && total === 0 ? (
          <h2><small>Žádná oblíbená témata</small></h2>
        ) : (
          <>
            {this.getTagId() === undefined && <Labels tags={tags} link={id => `?tag_id=${id}`} />}
            {
              fetching
                ? <SkeletonPreviews>{PER_PAGE}</SkeletonPreviews>
                : <Previews themes={themes} tagLink={id => `?tag_id=${id}`} />
            }
            <ActivePager
              name={PAGINATION_NAME}
              perPage={PER_PAGE}
              reset={this.state.reset}
              onReload={this.reload}
              total={total}
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
  tags: tags.getStarred(state),
  fetching: themes.isAllFetching(state) || tags.isStarredFetching(state),
  pagination: getSourcePagination(PAGINATION_NAME, { page: 1, perPage: PER_PAGE }, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.fetchStarred()),
  fetchStarred: (
    pagination: PaginationType,
    tagId: ?number,
  ) => dispatch(theme.fetchStarred(pagination, tagId)),
});
export default connect(mapStateToProps, mapDispatchToProps)(StarredThemes);
