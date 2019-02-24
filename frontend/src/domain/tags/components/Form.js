// @flow
import React from 'react';
import type { PostedTagType, TargetType } from '../types';

type Props = {|
  +onSubmit: (PostedTagType) => (any),
|};
type State = {|
  tag: PostedTagType,
|};
const initState = {
  tag: {
    name: '',
  },
};
export default class extends React.Component<Props, State> {
  state = initState;

  onChange = ({ target: { name, value } }: TargetType) => {
    const input = { [name]: value };
    this.setState(prevState => ({
      tag: {
        ...prevState.tag,
        ...input,
      },
    }));
  };

  onSubmit = () => {
    const { tag } = this.state;
    this.props.onSubmit(tag);
  };

  render() {
    const { tag } = this.state;
    return (
      <>
        <form>
          <div className="form-group">
            <label htmlFor="name">Název</label>
            <input
              type="text"
              className="form-control"
              id="name"
              name="name"
              value={tag.name}
              onChange={this.onChange}
            />
          </div>
        </form>
        <button type="button" tabIndex="0" className="btn btn-success" onClick={this.onSubmit}>Přidat</button>
      </>
    );
  }
}
