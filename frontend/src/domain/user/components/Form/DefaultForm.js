// @flow
import React from 'react';
import classNames from 'classnames';
import type { ErrorUserType, FetchedUserType, PostedUserType } from '../../types';
import * as validation from '../../validation';
import { fromFetchedToPosted } from '../../types';

type EventType = {|
  +target: {|
    +name: string,
    +value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedUserType) => (Promise<any>),
  +user: FetchedUserType,
|};
type State = {|
  user: PostedUserType,
  errors: ErrorUserType,
|};
const initStateErrors = {
  username: null,
};
const initState = {
  user: {
    username: '',
  },
  errors: initStateErrors,
};
class DefaultForm extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.setState({ user: fromFetchedToPosted(this.props.user) });
  }

  handleOnChange = ({ target: { name, value } }: EventType) => {
    this.setState(prevState => ({
      user: {
        ...prevState.user,
        [name]: value,
      },
    }));
  };

  handleSubmit = (event: { ...EventType, preventDefault: () => (void) }) => {
    event.preventDefault();
    const { user } = this.state;
    if (validation.anyErrors(user)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.user),
      }));
    } else {
      this.setState(
        prevState => ({ ...prevState, errors: initStateErrors }),
        () => this.props.onSubmit(user),
      );
    }
  };

  render() {
    const { user, errors } = this.state;
    return (
      <form className="form-horizontal">
        <div className={classNames('form-group', errors.username && 'has-error')}>
          <label htmlFor="username">Uživatelské jméno</label>
          <input name="username" value={user.username} onChange={this.handleOnChange} className="form-control" />
          {errors.username && <span className="help-block">{validation.toMessage(errors, 'username')}</span>}
        </div>
        <div className="form-group">
          <button type="button" onClick={this.handleSubmit} name="enter" className="btn btn-success">
            Upravit
          </button>
        </div>
      </form>
    );
  }
}

export default DefaultForm;
