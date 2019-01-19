import React from 'react';
import axios from 'axios';
import ReactDOM from 'react-dom';
import thunk from 'redux-thunk';
import logger from 'redux-logger';
import { Provider } from 'react-redux';
import { applyMiddleware, createStore } from 'redux';
import { createBrowserHistory } from 'history';
import 'jquery/src/jquery';
import 'bootstrap/dist/js/bootstrap.min';
import './App.css';
import Router from './router';
import combineReducers from './reducers';
import * as serviceWorker from './serviceWorker';
import withSettings from './api/connection';
import * as session from './domain/access/session';
import * as user from './domain/user/endpoints';
import { reSignIn } from './domain/sign/endpoints';

axios.defaults = withSettings(axios.defaults);

const history = createBrowserHistory();
history.listen((location) => {
  if (session.exists()) {
    const token = session.getValue();
    user.fetchMe(token, me => session.updateCredentials(me))
      .then(() => {
        if (session.expired()) {
          reSignIn(token, () => history.push('/sign/in', { state: { from: location } }));
        }
      })
      .catch(() => {
        session.destroy();
        history.push('/sign/in', { state: { from: location } });
      });
  }
});

const reduxMiddleWares = [
  thunk,
  process.env.NODE_ENV === 'development' ? logger : null,
].filter(Boolean);

ReactDOM.render(
  <Provider store={createStore(combineReducers, applyMiddleware(...reduxMiddleWares))}>
    <Router history={history} />
  </Provider>,
  document.getElementById('root'),
);

serviceWorker.unregister();
