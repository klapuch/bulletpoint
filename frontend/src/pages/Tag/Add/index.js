// @flow
import React from 'react';
import Form from '../../../domain/tags/components/Form';
import { add } from '../../../domain/tags/endpoints';
import type {PostedTagType} from "../../../domain/tags/types";

type Props = {|
  +history: Object,
|};
class Add extends React.Component<Props> {
  handleSubmit = (tag: PostedTagType) => {
    add(tag, () => this.props.history.push('/themes/create'));
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

export default Add;
