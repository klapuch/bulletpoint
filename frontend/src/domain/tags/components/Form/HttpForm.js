// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { PostedTagType } from '../../types';
import DefaultForm from './DefaultForm';
import * as tag from '../../actions';

type Props = {|
  +history: Object,
  +addTag: (PostedTagType) => (Promise<void>),
|};
class HttpForm extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    const next = () => Promise.resolve()
      .then(() => this.props.history.push('/themes/create'));
    this.props.addTag(tag, next);
  };

  render() {
    return (
      <DefaultForm onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  addTag: (postedTag: PostedTagType, next) => dispatch(tag.add(postedTag, next)),
});
export default connect(null, mapDispatchToProps)(HttpForm);
