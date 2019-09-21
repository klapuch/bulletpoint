// @flow
import React from 'react';
import { connect } from 'react-redux';
import type { PostedTagType } from '../../types';
import DefaultForm from './DefaultForm';
import * as tag from '../../actions';

type Props = {|
  +history: Object,
  +addTag: (PostedTagType, () => Promise<any>) => (void),
|};
class HttpForm extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    this.props.addTag(tag, () => this.props.history.push('/themes/create'));
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
