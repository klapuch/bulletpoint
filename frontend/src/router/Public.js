// @flow
import React from 'react';
import { Route, Link } from 'react-router-dom';
import classNames from 'classnames';
import Error404 from '../pages/Error/Error404';
import * as session from '../access/session';
import NavItem from './NavItem';
import FlashMessage from '../ui/message/FlashMessage';

type Props = {
  +component: any,
  +restrictive?: boolean,
  +title?: () => ?Object,
};
const Public = ({
  component: Component, restrictive = false, title = () => null, ...rest
}: Props): Route => (
  <Route
    {...rest}
    render={props => (
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
                  {session.exists() && <NavItem title="Nové téma" to="/themes/create">Nové téma</NavItem>}
                  {session.exists()
                    ? <NavItem title="Odhlásit se" to="/sign/out">Odhlásit se</NavItem>
                    : <NavItem title="Přihlásit se" to="/sign/in">Přihlásit se</NavItem>
                  }
                </ul>
              </div>
            </div>
          </nav>
          <div className="container">
            <div className="row">
              <FlashMessage />
            </div>
            {restrictive && !session.exists()
              ? <Error404 {...props} />
              : <Component {...props} />}
          </div>
        </div>
        <div id="footer">
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
