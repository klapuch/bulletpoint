// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import getSlug from 'speakingurl';
import { change, single } from '../../../theme/endpoints';
import Form from '../../../theme/Form';
import { getAll, allFetching as tagsFetching } from '../../../tags/selects';
import { all } from '../../../tags/endpoints';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType, PostedThemeType } from '../../../theme/types';
import type { TagType } from '../../../tags/types';
import { getById, singleFetching as themeFetching } from '../../../theme/selects';

type Props = {|
  +changeTheme: (number, PostedThemeType, () => (void)) => (void),
  +fetchSingle: (number) => (void),
  +fetchTags: () => (void),
  +fetching: boolean,
  +history: Object,
  +match: Object,
  +tags: Array<TagType>,
  +theme: FetchedThemeType,
|};
class Create extends React.Component<Props> {
  componentDidMount(): void {
    const { match: { params: { id } } } = this.props;
    this.props.fetchTags();
    this.props.fetchSingle(id);
  }

  handleSubmit = (theme: PostedThemeType) => {
    const { match: { params: { id } } } = this.props;
    this.props.changeTheme(id, theme, () => this.props.history.push(`/themes/${id}/${getSlug(theme.name)}`));
  };

  getTitle = (name: string) => `Úprava tématu "${name}"`;

  render() {
    const { fetching, tags, theme } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <Helmet>
          <title>{this.getTitle(theme.name)}</title>
        </Helmet>
        <h1>{this.getTitle(theme.name)}</h1>
        <Form theme={theme} tags={tags} onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id } } }) => ({
  tags: getAll(state),
  theme: getById(id, state),
  fetching: tagsFetching(state) || themeFetching(id, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(all()),
  fetchSingle: (id: number) => dispatch(single(id)),
  changeTheme: (
    id: number,
    theme: PostedThemeType,
    next: () => (void),
  ) => dispatch(change(id, theme, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
