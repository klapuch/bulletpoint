// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { PostedTagType } from '../types';
import Form from './Form';
import * as tag from '../endpoints';

type Props = {|
  +history: Object,
  +addTag: (PostedTagType, (void) => (void)) => (void),
|};
class HttpForm extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    this.props.addTag(tag, () => this.props.history.push('/themes/create'));
  };

  render() {
    return (
      <Form onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  addTag: (postedTag: PostedTagType, next: (void) => (void)) => dispatch(tag.add(postedTag, next)),
});
export default connect(null, mapDispatchToProps)(HttpForm);
