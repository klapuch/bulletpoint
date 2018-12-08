// @flow
import React from 'react';
import { Route } from 'react-router-dom';
import Error404 from '../pages/Error/Error404';

type Props = {
  +component: any,
  +restrictive?: boolean,
};
const Public = ({ component: Component, restrictive = false, ...rest }: Props): Route => (
  <Route
    {...rest}
    render={props => (
      <>
        <div id="wrap">
          <nav className="navbar navbar-default navbar-static-top">
            <div className="container">
              <div className="navbar-header">
                <button type="button" className="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                  <span className="sr-only">Toggle navigation</span>
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                </button>
                <a className="navbar-brand" href="Default:" title="bulletpoint"><strong>bulletpoint</strong></a>
              </div>
              <div id="navbar" className="navbar-collapse collapse">
                <ul className="nav navbar-nav">
                  <li title="Nový dokument">
                    <a href="Prochazet:dokumenty">Procházet</a>
                  </li>
                  <li className="dropdown">
                    <a href="#" className="dropdown-toggle" title="Moje" data-toggle="dropdown" role="button" aria-expanded="false">Moje
                      <span className="caret"></span>
                    </a>
                    <ul className="dropdown-menu" role="menu">
                      <li title="Moje dokumenty">
                        <a href="Moje:dokumenty">Dokumenty</a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
          <div className="container">
            {restrictive
              ? <Public component={Error404} {...props} />
              : <Component {...props} />}
          </div>
        </div>
        <div id="footer">
          <div className="container">
            <p className="muted credit text-center">
              <a href="https://github.com/klapuch/bulletpoint" className="no-link"
                 target="_blank">
                Created with <span id="heart">❤</span>
              </a>
            </p>
          </div>
        </div>
      </>
    )}
  />
);

export default Public;
