// @flow
import React from 'react';
import { Route, Redirect } from 'react-router-dom';
import * as user from '../domain/user';
import Public from './Public';

type Props = {
  +component: any,
};
const Private = ({ component: Component, ...rest }: Props): Route => (
  <Route
    {...rest}
    render={props => (
      user.isLoggedIn()
        ? <Public component={Component} {...props} />
        : <Redirect to={{ pathname: '/sign/in', state: { from: props.location } }} />
    )}
  />
);

export default Private;
