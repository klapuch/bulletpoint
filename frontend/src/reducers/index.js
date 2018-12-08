// @flow
import { combineReducers } from 'redux';
import theme from '../theme/reducer';
import themeBulletpoints from '../theme/bulletpoint/reducer';

export default combineReducers({
  theme,
  themeBulletpoints,
});
