// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import getSlug from 'speakingurl';
import * as tag from '../../../domain/tags/endpoints';
import * as tags from '../../../domain/tags/selects';
import * as theme from '../../../domain/theme/endpoints';
import Form from '../../../domain/theme/components/Form';
import Loader from '../../../ui/Loader';
import type { PostedThemeType } from '../../../domain/theme/types';
import type { TagType } from '../../../domain/tags/types';

type Props = {|
  +history: Object,
  +fetchTags: (void) => (void),
  +fetching: boolean,
  +tags: Array<TagType>,
|};
class Create extends React.Component<Props> {
  componentDidMount(): void {
    this.props.fetchTags();
  }

  handleSubmit = (postedTheme: PostedThemeType) => {
    theme.create(postedTheme, (id: number) => {
      this.props.history.push(`/themes/${id}/${getSlug(postedTheme.name)}`);
    });
  };

  render() {
    const { fetching, tags } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <h1>Nové téma</h1>
        <Form tags={tags} onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapStateToProps = state => ({
  tags: tags.getAll(state),
  fetching: tags.allFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(tag.all()),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
