// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { connect } from 'react-redux';
import type { FetchedThemeType, PostedThemeType } from '../../types';
import * as theme from '../../endpoints';
import * as tags from '../../../tags/selects';
import DefaultForm from './DefaultForm';
import type { FetchedTagType } from '../../../tags/types';
import * as tag from '../../../tags/endpoints';

type Props = {|
  +history: Object,
  +match: Object,
  +fetching: boolean,
  +changeTheme: (number, PostedThemeType) => (Promise<any>),
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
    return this.props.changeTheme(id, theme)
      .then(() => this.props.history.push(`/themes/${id}/${getSlug(theme.name)}`));
  };

  render() {
    const { fetching, theme, tags } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <DefaultForm theme={theme} tags={tags} onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.fetchAll()),
  changeTheme: (
    id: number,
    postedTheme: PostedThemeType,
  ) => dispatch(theme.change(id, postedTheme))
    .then(() => dispatch(theme.fetchSingle(id))),
});

const mapStateToProps = state => ({
  tags: tags.getAll(state),
  fetching: tags.isFetching(state),
});
export default connect(mapStateToProps, mapDispatchToProps)(ChangeHttpForm);
