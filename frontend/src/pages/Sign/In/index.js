// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Redirect } from 'react-router-dom';
import FacebookLogin from 'react-facebook-login/dist/facebook-login-render-props';
import GoogleLogin from 'react-google-login';
import Form from '../../../domain/sign/components/Form';
import * as sign from '../../../domain/sign/endpoints';
import type { PostedCredentialsType, PostedProviderCredentialsType, ProviderTypes } from '../../../domain/sign/types';
import { FACEBOOK_PROVIDER, GOOGLE_PROVIDER, INTERNAL_PROVIDER } from '../../../domain/sign/types';
import SocialLoginButton from '../../../components/SocialLoginButton';
import * as message from '../../../ui/message/actions';

type Props = {|
  +receivedError: (string),
  +signIn: (
    ProviderTypes,
    PostedCredentialsType|PostedProviderCredentialsType,
  ) => (Promise<void>),
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
    this.props.signIn(INTERNAL_PROVIDER, credentials)
      .then(this.afterLogin)
      // $FlowFixMe correct string from endpoint.js
      .catch(this.props.receivedError);
  };

  handleFacebookLogin = (credentials: Object) => {
    if (typeof credentials.accessToken !== 'undefined') {
      this.props.signIn(FACEBOOK_PROVIDER, { login: credentials.accessToken })
        .then(this.afterLogin)
        // $FlowFixMe correct string from endpoint.js
        .catch(this.props.receivedError);
    }
  };

  handleGoogleLogin = (credentials: Object) => {
    if (typeof credentials.accessToken !== 'undefined') {
      this.props.signIn(GOOGLE_PROVIDER, { login: credentials.accessToken })
        .then(this.afterLogin)
        // $FlowFixMe correct string from endpoint.js
        .catch(this.props.receivedError);
    }
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
            render={renderProps => (
              <SocialLoginButton provider="facebook" onClick={renderProps.onClick}>
                Login with facebook
              </SocialLoginButton>
            )}
          />
          <br />
          <GoogleLogin
            clientId={process.env.REACT_APP_GOOGLE_CLIENT_ID}
            buttonText="LOGIN WITH GOOGLE"
            onSuccess={this.handleGoogleLogin}
            onFailure={this.handleGoogleLogin}
            render={renderProps => (
              <SocialLoginButton provider="google" onClick={renderProps.onClick}>
                Login with Google&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </SocialLoginButton>
            )}
          />
        </div>

      </>
    );
  }
}

const mapDispatchToProps = dispatch => ({
  receivedError: error => dispatch(message.receivedError(error)),
  signIn: (
    provider: ProviderTypes,
    credentials: PostedCredentialsType,
  ) => dispatch(sign.signIn(provider, credentials))
    .then(() => message.receivedSuccess('Jsi úspěšně přihlášen.')),
});
export default connect(null, mapDispatchToProps)(In);
