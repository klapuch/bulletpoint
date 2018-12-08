import React from 'react';
import axios from 'axios';
import ReactDOM from 'react-dom';
import thunk from 'redux-thunk';
import logger from 'redux-logger';
import { Provider } from 'react-redux';
import { applyMiddleware, createStore } from 'redux';
import { createBrowserHistory } from 'history';
import Router from './router';
import combineReducers from './reducers';
import * as serviceWorker from './serviceWorker';
import withSettings from './api/connection';

axios.defaults = withSettings(axios.defaults);

const history = createBrowserHistory();

ReactDOM.render(
	<Provider store={createStore(combineReducers, applyMiddleware(thunk, logger))}>
		<Router history={history} />
	</Provider>,
	document.getElementById('root'),
);

serviceWorker.unregister();
