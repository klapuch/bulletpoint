import React from 'react';
import axios from 'axios';
import ReactDOM from 'react-dom';
import logger from 'redux-logger';
import { Provider } from 'react-redux';
import { applyMiddleware, createStore } from 'redux';
import { createBrowserHistory } from 'history';
import axiosRetry from 'axios-retry';
import createSagaMiddleware from 'redux-saga';
import 'jquery/src/jquery';
import 'bootstrap/dist/js/bootstrap.min';
import './App.css';
import Router from './router';
import combineReducers from './reducers';
import rootSaga from './sagas';
import * as serviceWorker from './serviceWorker';
import withSettings from './api/connection';
import * as session from './domain/access/session';
import * as user from './domain/user/endpoints';
import * as sign from './domain/sign/endpoints';
import ErrorBoundary from './api/ErrorBoundary';

axios.defaults = withSettings(axios.defaults);

axiosRetry(axios, { retries: 3 });

const history = createBrowserHistory();
history.listen((location) => {
  const token = session.getValue();
  if (token !== null && location.pathname !== '/sign/out') {
    user.refresh(token)
      .then(() => {
        if (session.expired()) {
          return sign.reSignIn(token);
        }
        return Promise.resolve();
      })
      .catch(session.destroy)
      .catch(() => history.push('/sign/in', { state: { from: location } }));
  }
});

const sagaMiddleware = createSagaMiddleware();

const reduxMiddleWares = [
  process.env.NODE_ENV === 'development' ? logger : null,
  sagaMiddleware,
].filter(Boolean);

const store = createStore(combineReducers, applyMiddleware(...reduxMiddleWares));

sagaMiddleware.run(rootSaga);

ReactDOM.render(
  <ErrorBoundary>
    <Provider store={store}>
      <Router history={history} />
    </Provider>
  </ErrorBoundary>,
  document.getElementById('root'),
);

serviceWorker.unregister();
