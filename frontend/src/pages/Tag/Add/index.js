// @flow
import React from 'react';
import { connect } from 'react-redux';
import Form from '../../../domain/tags/components/Form';
import * as tag from '../../../domain/tags/endpoints';
import type { PostedTagType } from '../../../domain/tags/types';

type Props = {|
  +history: Object,
  +addTag: (PostedTagType, (void) => (void)) => (void),
|};
class Add extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    this.props.addTag(tag, () => this.props.history.push('/themes/create'));
  };

  render() {
    return (
      <>
        <h1>Přidat tag</h1>
        <Form onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  addTag: (postedTag: PostedTagType, next: (void) => (void)) => dispatch(tag.add(postedTag, next)),
});
export default connect(null, mapDispatchToProps)(Add);
