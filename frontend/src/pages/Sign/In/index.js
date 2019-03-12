// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Redirect } from 'react-router-dom';
import FacebookLogin from 'react-facebook-login';
import GoogleLogin from 'react-google-login';
import Form from '../../../domain/sign/components/Form';
import * as sign from '../../../domain/sign/endpoints';
import type { PostedCredentialsType, PostedProviderCredentialsType, ProviderTypes } from '../../../domain/sign/types';
import { FACEBOOK_PROVIDER, GOOGLE_PROVIDER, INTERNAL_PROVIDER } from '../../../domain/sign/types';

type Props = {|
  +signIn: (
    ProviderTypes,
    PostedCredentialsType|PostedProviderCredentialsType,
    () => (void),
  ) => (void),
  +location: Object,
|};
type State = {|
  redirectToReferrer: boolean,
|};
class In extends React.Component<Props, State> {
  state = {
    redirectToReferrer: false,
  };

  afterLogin = () => {
    this.setState({ redirectToReferrer: true });
  };

  handleSubmit = (credentials: PostedCredentialsType) => {
    this.props.signIn(
      INTERNAL_PROVIDER,
      credentials,
      this.afterLogin,
    );
  };

  handleFacebookLogin = (credentials: Object) => {
    this.props.signIn(
      FACEBOOK_PROVIDER,
      { login: credentials.accessToken },
      this.afterLogin,
    );
  };

  handleGoogleLogin = (credentials: Object) => {
    this.props.signIn(
      GOOGLE_PROVIDER,
      { login: credentials.accessToken },
      this.afterLogin,
    );
  };

  render() {
    const { from } = this.props.location.state || { from: { pathname: '/' } };
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
        <div className="text-center">
          <FacebookLogin
            appId={process.env.REACT_APP_FACEBOOK_APP_ID}
            fields="email"
            callback={this.handleFacebookLogin}
          />
          <br />
          <GoogleLogin
            clientId={process.env.REACT_APP_GOOGLE_CLIENT_ID}
            buttonText="LOGIN WITH GOOGLE"
            onSuccess={this.handleGoogleLogin}
            onFailure={this.handleGoogleLogin}
          />
        </div>

      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  signIn: (
    provider: ProviderTypes,
    credentials: PostedCredentialsType,
    next: () => (void),
  ) => dispatch(sign.signIn(provider, credentials, next)),
});
export default connect(null, mapDispatchToProps)(In);
