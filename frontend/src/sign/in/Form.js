// @flow
import React from 'react';
import classNames from 'classnames';
import type { PostedCredentialsType, ErrorCredentialsType } from '../types';
import * as validation from '../validation';

type EventType = {|
  +target: {|
    +name: string,
    +value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedCredentialsType) => (void),
|};
type State = {|
  credentials: PostedCredentialsType,
  errors: ErrorCredentialsType,
|};
const initStateErrors = {
  email: null,
  password: null,
};
class Form extends React.Component<Props, State> {
  state = {
    credentials: {
      email: 'klapuchdominik@gmail.com',
      password: 'heslo123',
    },
    errors: initStateErrors,
  };

  onChange = ({ target: { name, value } }: EventType) => {
    this.setState(prevState => ({
      credentials: {
        ...prevState.credentials,
        [name]: value,
      },
    }));
  };

  onSubmit = (event: { ...EventType, preventDefault: () => (void) }) => {
    event.preventDefault();
    if (validation.anyErrors(this.state.credentials)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.credentials),
      }));
    } else {
      this.props.onSubmit(this.state.credentials);
      this.setState(prevState => ({ ...prevState, errors: initStateErrors }));
    }
  };

  render() {
    const { credentials, errors } = this.state;
    return (
      <form className="form-horizontal">
        <div className={classNames('form-group', errors.email && 'has-error')}>
          <label htmlFor="email">E-mail</label>
          <input name="email" value={credentials.email} onChange={this.onChange} className="form-control" placeholder="E-mail" />
          {errors.email && <span className="help-block">{validation.toMessage(errors, 'email')}</span>}
        </div>
        <div className={classNames('form-group', errors.password && 'has-error')}>
          <label htmlFor="password">Heslo</label>
          <input type="password" value={credentials.password} name="password" onChange={this.onChange} className="form-control" placeholder="Heslo" />
          {errors.password && <span className="help-block">{validation.toMessage(errors, 'password')}</span>}
        </div>
        <div className="form-group">
          <button type="button" onClick={this.onSubmit} name="enter" className="btn btn-success">
            Přihlásit se
          </button>
        </div>
      </form>
    );
  }
}

export default Form;
