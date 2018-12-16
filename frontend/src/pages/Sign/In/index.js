// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Redirect } from 'react-router-dom';
import Form from '../../../sign/in/Form';
import { signIn } from '../../../sign/endpoints';
import type { PostedCredentialsType } from '../../../sign/types';

type Props = {|
  +signIn: (PostedCredentialsType, () => (void)) => (void),
  +location: Object,
|};
type State = {|
  redirectToReferrer: boolean,
|};
class In extends React.Component<Props, State> {
  state = {
    redirectToReferrer: false,
  };

  handleSubmit = (credentials: PostedCredentialsType) => {
    this.props.signIn(
      credentials,
      () => this.setState({ redirectToReferrer: true }),
    );
  };

  render() {
    const { from } = this.props.location.state || { from: { pathname: '/themes' } };
    const { redirectToReferrer } = this.state;
    if (redirectToReferrer) {
      return <Redirect to={from} />;
    }
    return (
      <>
        <div className="row">
          <h1>Přihlášení</h1>
        </div>
        <Form onSubmit={this.handleSubmit} />
      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  signIn: (
    credentials: PostedCredentialsType,
    next: () => (void),
  ) => dispatch(signIn(credentials, next)),
});
export default connect(null, mapDispatchToProps)(In);
