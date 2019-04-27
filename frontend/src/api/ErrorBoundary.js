// @flow
import React from 'react';
import * as Sentry from '@sentry/browser';
import * as user from '../domain/user';

Sentry.init({
  dsn: process.env.REACT_APP_SENTRY_DSN,
  environment: process.env.REACT_APP_SENTRY_ENVIRONMENT,
});

type Props = {|
  +children: any,
|};

export default class extends React.Component<Props> {
  componentDidCatch(error: Object, errorInfo: Object) {
    Sentry.withScope((scope) => {
      scope.setExtras(errorInfo);
      scope.setUser(user.getMe());
    });
  }

  render() {
    return this.props.children;
  }
}
