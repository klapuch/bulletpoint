// @flow
import React from 'react';
import { Route, Link } from 'react-router-dom';
import classNames from 'classnames';
import * as user from '../domain/user';
import NavItem from './NavItem';
import FlashMessage from '../ui/message/FlashMessage';

type Props = {
  +component: any,
  +title?: () => ?Object,
};
const Public = ({
  component: Component, title = () => null, ...rest
}: Props): Route => (
  <Route
    {...rest}
    render={(props: { location: Object }) => (
      <>
        {title()}
        <div id="wrap">
          <nav className="navbar navbar-default navbar-static-top">
            <div className="container">
              <div className="navbar-header">
                <button
                  type="button"
                  className="navbar-toggle collapsed"
                  data-toggle="collapse"
                  data-target="#navbar"
                  aria-expanded="false"
                  aria-controls="navbar"
                >
                  <span className="sr-only">Toggle navigation</span>
                  <span className="icon-bar" />
                  <span className="icon-bar" />
                  <span className="icon-bar" />
                </button>
                <Link to="/" className="navbar-brand" title="bulletpoint"><strong>bulletpoint</strong></Link>
              </div>
              <div id="navbar" className="navbar-collapse collapse">
                <ul className="nav navbar-nav">
                  <Route
                    path="/themes/recent"
                    exact
                    children={({ match }) => ( // eslint-disable-line
                      <li className={classNames('dropdown', match && 'active')}>
                        <a href="#" className="dropdown-toggle" title="Témata" data-toggle="dropdown" role="button" aria-expanded="false">
                          Témata
                          <span className="caret" />
                        </a>
                        <ul className="dropdown-menu" role="menu">
                          <NavItem exact to="/themes/recent">Nedávno přidaná</NavItem>
                        </ul>
                      </li>
                    )}
                  />
                  {user.isAdmin() && <NavItem title="Nové téma" to="/themes/create">Nové téma</NavItem>}
                  {user.isAdmin() && <NavItem title="Přidat tag" to="/tags/add">Přidat tag</NavItem>}
                  {user.isLoggedIn()
                    ? <NavItem title="Odhlásit se" to="/sign/out">Odhlásit se</NavItem>
                    : <NavItem title="Přihlásit se" to="/sign/in">Přihlásit se</NavItem>
                  }
                </ul>
              </div>
            </div>
          </nav>
          <div className="container">
            <div className="row">
              <FlashMessage pathname={props.location.pathname} />
            </div>
            {<Component {...props} />}
          </div>
        </div>
        <div className="footer navbar-fixed-bottom">
          <div className="container">
            <p className="muted credit text-center">
              <a
                href="https://github.com/klapuch/bulletpoint"
                className="no-link"
                target="_blank"
                rel="noopener noreferrer"
              >
                Created with
                {' '}
                <span id="heart">❤</span>
              </a>
            </p>
          </div>
        </div>
      </>
    )}
  />
);

export default Public;
