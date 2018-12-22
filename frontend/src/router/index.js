// @flow
import React from 'react';
import { Router, Switch } from 'react-router-dom';
import Helmet from 'react-helmet';
import Default from '../pages/Default';
import Error404 from '../pages/Error/Error404';
import Public from './Public';
import { default as Theme } from '../pages/Theme';
import { default as Themes } from '../pages/Themes';
import { default as ThemesSearchResults } from '../pages/Themes/SearchResults';
import { default as CreateTheme } from '../pages/Theme/Create';
import { default as ChangeTheme } from '../pages/Theme/Change';
import { default as SignIn } from '../pages/Sign/In';
import { default as SignOut } from '../pages/Sign/Out';

const Title = ({ children }: {| +children?: string |}) => (
  <Helmet titleTemplate="%s | Bulletpoint" defaultTitle="Bulletpoint">
    <title>{children}</title>
  </Helmet>
);

type Props = {|
  +history: Object,
|};
export default ({ history }: Props) => (
  <Router history={history}>
    <Switch>
      <Public exact path="/" component={Default} title={() => <Title />} />
      <Public restrictive path="/themes/create" component={CreateTheme} title={() => <Title>Nové téma</Title>} />
      <Public restrictive path="/themes/:id/change" component={ChangeTheme} title={() => <Title />} />
      <Public path="/themes/tag/:tag" component={Themes} title={() => <Title />} />
      <Public path="/themes/search" component={ThemesSearchResults} title={() => <Title />} />
      <Public path="/themes/recent" component={Themes} title={() => <Title>Nedávno přidaná témata</Title>} />
      <Public path="/themes/:id" component={Theme} title={() => <Title />} />
      <Public path="/sign/in" component={SignIn} title={() => <Title>Přihlášení</Title>} />
      <Public path="/sign/out" component={SignOut} />
      <Public component={Error404} />
    </Switch>
  </Router>
);
