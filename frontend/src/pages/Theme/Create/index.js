// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import { create } from '../../../theme/endpoints';
import Form from '../../../theme/Form';
import { getAll, allFetching as tagsFetching } from '../../../tags/selects';
import { all } from '../../../tags/endpoints';
import Loader from '../../../ui/Loader';
import type { PostedThemeType } from '../../../theme/types';
import type { TagType } from '../../../tags/types';

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

  handleSubmit = (theme: PostedThemeType) => {
    create(theme, (id: number) => {
      this.props.history.push(`/themes/${id}`);
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
  tags: getAll(state),
  fetching: tagsFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchTags: () => dispatch(all()),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
