// @flow
import { combineReducers } from 'redux';
import theme from '../theme/reducer';
import themeBulletpoints from '../theme/bulletpoint/reducer';
import tags from '../tags/reducer';

export default combineReducers({
  theme,
  themeBulletpoints,
  tags,
});
