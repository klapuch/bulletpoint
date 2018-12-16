// @flow
import React from 'react';
import type { PostedCredentialsType } from '../endpoints';

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
  credentials: PostedCredentialsType
|};
class Form extends React.Component<Props, State> {
  state = {
    credentials: {
      email: 'klapuchdominik@gmail.com',
      password: 'heslo123',
    },
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
    this.props.onSubmit(this.state.credentials);
  };

  render() {
    const { credentials: { email, password } } = this.state;
    return (
      <form className="form-horizontal">
        <div className="form-group">
          <label htmlFor="email">E-mail</label>
          <input name="email" value={email} onChange={this.onChange} className="form-control" placeholder="E-mail" />
        </div>
        <div className="form-group">
          <label htmlFor="password">Heslo</label>
          <input type="password" value={password} name="password" onChange={this.onChange} className="form-control" placeholder="Heslo" />
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
