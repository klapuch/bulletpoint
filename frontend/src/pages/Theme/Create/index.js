// @flow
import React from 'react';
import { create } from '../../../theme/endpoints';
import Add from '../../../theme/Add';

type Props = {|
  +history: Object,
|};
class Create extends React.Component<Props> {
  componentDidMount(): void {

  }

  onSubmit = (theme: Object) => {
    create(theme, (id: number) => {
      this.props.history.push(`/themes/${id}`);
    });
  };

  render() {
    return (
      <>
        <h1>Nové téma</h1>
        <Add onSubmit={this.onSubmit} />
      </>
    );
  }
}

export default Create;
