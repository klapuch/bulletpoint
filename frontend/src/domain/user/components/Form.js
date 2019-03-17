// @flow
import React from 'react';
import classNames from 'classnames';
import type {PostedCredentialsType, ErrorUserType, PostedUserType} from '../types';
import * as validation from '../validation';

type EventType = {|
  +target: {|
    +name: string,
    +value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedUserType) => (void),
|};
type State = {|
  user: PostedUserType,
  errors: ErrorUserType,
|};
const initStateErrors = {
  username: null,
};
class Form extends React.Component<Props, State> {
  state = {
    user: {
      username: '',
    },
    errors: initStateErrors,
  };

  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    this.setState({ user: this.props.user });
  };

  onChange = ({ target: { name, value } }: EventType) => {
    this.setState(prevState => ({
      user: {
        ...prevState.user,
        [name]: value,
      },
    }));
  };

  onSubmit = (event: { ...EventType, preventDefault: () => (void) }) => {
    event.preventDefault();
    if (validation.anyErrors(this.state.user)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.user),
      }));
    } else {
      this.props.onSubmit(this.state.user);
      this.setState(prevState => ({ ...prevState, errors: initStateErrors }));
    }
  };

  render() {
    const { user, errors } = this.state;
    return (
      <form className="form-horizontal">
        <div className={classNames('form-group', errors.username && 'has-error')}>
          <label htmlFor="username">Uživatelské jméno</label>
          <input name="username" value={user.username} onChange={this.onChange} className="form-control" />
          {errors.username && <span className="help-block">{validation.toMessage(errors, 'username')}</span>}
        </div>
        <div className="form-group">
          <button type="button" onClick={this.onSubmit} name="enter" className="btn btn-success">
            Upravit
          </button>
        </div>
      </form>
    );
  }
}

export default Form;
