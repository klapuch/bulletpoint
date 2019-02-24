// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import getSlug from 'speakingurl';
import * as tag from '../../../domain/tags/endpoints';
import * as tags from '../../../domain/tags/selects';
import * as theme from '../../../domain/theme/endpoints';
import * as themes from '../../../domain/theme/selects';
import Form from '../../../domain/theme/components/Form';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType, PostedThemeType } from '../../../domain/theme/types';
import type { FetchedTagType } from '../../../domain/tags/types';

type Props = {|
  +changeTheme: (number, PostedThemeType, () => (void)) => (void),
  +fetchSingle: (number) => (void),
  +fetchTags: () => (void),
  +fetching: boolean,
  +history: Object,
  +match: Object,
  +tags: Array<FetchedTagType>,
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
  tags: tags.getAll(state),
  theme: themes.getById(id, state),
  fetching: tags.allFetching(state) || themes.singleFetching(id, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.all()),
  fetchSingle: (id: number) => dispatch(theme.single(id)),
  changeTheme: (
    id: number,
    postedTheme: PostedThemeType,
    next: () => (void),
  ) => dispatch(theme.change(id, postedTheme, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
