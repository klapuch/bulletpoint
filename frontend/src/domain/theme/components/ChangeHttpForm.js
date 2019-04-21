// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { connect } from 'react-redux';
import type { FetchedThemeType, PostedThemeType } from '../types';
import * as theme from '../endpoints';
import * as tags from '../../tags/selects';
import Form from './Form';
import type { FetchedTagType } from '../../tags/types';
import * as tag from '../../tags/endpoints';

type Props = {|
  +history: Object,
  +match: Object,
  +fetching: boolean,
  +changeTheme: (number, PostedThemeType, () => (void)) => (Promise<any>),
  +theme: FetchedThemeType,
  +fetchTags: () => (void),
  +tags: Array<FetchedTagType>,
|};
class ChangeHttpForm extends React.Component<Props> {
  componentDidMount(): void {
    this.props.fetchTags();
  }

  handleSubmit = (theme: PostedThemeType) => {
    const { match: { params: { id } } } = this.props;
    return this.props.changeTheme(
      id,
      theme,
      () => this.props.history.push(`/themes/${id}/${getSlug(theme.name)}`),
    );
  };

  render() {
    const { fetching, theme, tags } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <Form theme={theme} tags={tags} onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.fetchAll()),
  changeTheme: (
    id: number,
    postedTheme: PostedThemeType,
    next: () => (void),
  ) => dispatch(theme.change(id, postedTheme, next)),
});

const mapStateToProps = state => ({
  tags: tags.getAll(state),
  fetching: tags.allFetching(state),
});
export default connect(mapStateToProps, mapDispatchToProps)(ChangeHttpForm);
