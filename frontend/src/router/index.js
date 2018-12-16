// @flow
import React from 'react';
import { Router, Switch } from 'react-router-dom';
import Default from '../pages/Default';
import Error404 from '../pages/Error/Error404';
import Public from './Public';
import { default as Theme } from '../pages/Theme';
import { default as Themes } from '../pages/Themes';
import { default as CreateTheme } from '../pages/Theme/Create';
import { default as SignIn } from '../pages/Sign/In';
import { default as SignOut } from '../pages/Sign/Out';

type Props = {|
  +history: Object,
|};
export default ({ history }: Props) => (
  <Router history={history}>
    <Switch>
      <Public exact path="/" component={Default} />
      <Public path="/themes/create" component={CreateTheme} />
      <Public path="/themes/:id" component={Theme} />
      <Public path="/themes" component={Themes} />
      <Public path="/sign/in" component={SignIn} />
      <Public path="/sign/out" component={SignOut} />
      <Public component={Error404} />
    </Switch>
  </Router>
);
