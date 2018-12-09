// @flow
import React from 'react';
import connect from 'react-redux/es/connect/connect';
import { create } from '../../../theme/endpoints';
import Add from '../../../theme/Add';
import { getAll, allFetching as tagsFetching } from '../../../tags/selects';
import { all } from '../../../tags/endpoints';
import Loader from '../../../ui/Loader';

type Props = {|
  +history: Object,
  +getAllTags: (void) => (void),
  +fetching: boolean,
  +tags: Array<Object>,
|};
class Create extends React.Component<Props> {
  componentDidMount(): void {
    this.props.getAllTags();
  }

  onSubmit = (theme: Object) => {
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
        <Add tags={tags} onSubmit={this.onSubmit} />
      </>
    );
  }
}

const mapStateToProps = state => ({
  tags: getAll(state),
  fetching: tagsFetching(state),
});
const mapDispatchToProps = dispatch => ({
  getAllTags: () => dispatch(all()),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
