// @flow
import React from 'react';
import classNames from 'classnames';
import type { ErrorTagType, PostedTagType } from '../../types';
import * as validation from '../../validation';

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedTagType) => (any),
|};
type State = {|
  tag: PostedTagType,
  errors: ErrorTagType,
|};
const initState = {
  tag: {
    name: '',
  },
  errors: validation.initErrors,
};
export default class extends React.Component<Props, State> {
  state = initState;

  handleChange = ({ target: { name, value } }: TargetType) => {
    const input = { [name]: value };
    this.setState(prevState => ({
      tag: {
        ...prevState.tag,
        ...input,
      },
    }));
  };

  handleSubmit = () => {
    const { tag } = this.state;
    if (validation.anyErrors(tag)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.tag),
      }));
    } else {
      this.props.onSubmit(tag);
    }
  };

  render() {
    const { tag, errors } = this.state;
    return (
      <>
        <form>
          <div className={classNames('form-group', errors.name && 'has-error')}>
            <label htmlFor="name">Název</label>
            <input
              type="text"
              className="form-control"
              id="name"
              name="name"
              value={tag.name}
              onChange={this.handleChange}
            />
            {errors.name && <span className="help-block">{validation.toMessage(errors, 'name')}</span>}
          </div>
        </form>
        <button type="button" tabIndex="0" className="btn btn-success" onClick={this.handleSubmit}>Přidat</button>
      </>
    );
  }
}
