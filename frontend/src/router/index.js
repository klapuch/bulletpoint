// @flow
import React from 'react';
import { Router, Switch } from 'react-router-dom';
import Default from '../pages/Default';
import Error404 from '../pages/Error/Error404';
import Public from './Public';
import { default as Theme } from '../pages/Theme';

type Props = {|
  +history: Object,
|};
export default ({ history }: Props) => (
  <Router history={history}>
    <Switch>
      <Public exact path="/" component={Default} />
      <Public path="/themes/:id" component={Theme} />
      <Public component={Error404} />
    </Switch>
  </Router>
);
