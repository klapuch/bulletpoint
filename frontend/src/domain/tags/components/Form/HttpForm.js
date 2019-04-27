// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { PostedTagType } from '../../types';
import DefaultForm from './DefaultForm';
import * as tag from '../../endpoints';
import * as message from '../../../../ui/message/actions';

type Props = {|
  +history: Object,
  +addTag: (PostedTagType) => (Promise<void>),
  +receivedError: (string),
|};
class HttpForm extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    this.props.addTag(tag)
      .then(() => this.props.history.push('/themes/create'))
      // $FlowFixMe correct string from endpoint.js
      .catch(this.props.receivedError);
  };

  render() {
    return (
      <DefaultForm onSubmit={this.handleSubmit} />
    );
  }
}

const mapDispatchToProps = dispatch => ({
  receivedError: error => dispatch(message.receivedError(error)),
  addTag: (postedTag: PostedTagType) => tag.add(postedTag),
});
export default connect(null, mapDispatchToProps)(HttpForm);
