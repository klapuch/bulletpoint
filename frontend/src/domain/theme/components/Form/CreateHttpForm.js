// @flow
import React from 'react';
import getSlug from 'speakingurl';
import { connect } from 'react-redux';
import type { PostedThemeType } from '../../types';
import * as theme from '../../endpoints';
import * as tags from '../../../tags/selects';
import DefaultForm from './DefaultForm';
import type { FetchedTagType } from '../../../tags/types';
import * as tag from '../../../tags/endpoints';

type Props = {|
  +history: Object,
  +fetchTags: (void) => (void),
  +fetching: boolean,
  +tags: Array<FetchedTagType>,
|};
class ChangeHttpForm extends React.Component<Props> {
  componentDidMount(): void {
    this.props.fetchTags();
  }

  handleSubmit = (postedTheme: PostedThemeType) => theme.create(postedTheme)
    .then(id => this.props.history.push(`/themes/${id}/${getSlug(postedTheme.name)}`))
  ;

  render() {
    const { fetching, tags } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <DefaultForm tags={tags} onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.fetchAll()),
});

const mapStateToProps = state => ({
  tags: tags.getAll(state),
  fetching: tags.isFetching(state),
});
export default connect(mapStateToProps, mapDispatchToProps)(ChangeHttpForm);
