// @flow
import { combineReducers } from 'redux';
import theme from '../theme/reducer';
import themeBulletpoints from '../theme/bulletpoint/reducer';
import tags from '../tags/reducer';
import message from '../ui/reducer';
import { SIGN_IN, SIGN_OUT } from '../sign/actions';

const appReducer = combineReducers({
  theme,
  themeBulletpoints,
  tags,
  message,
});

export default function rootReducer(state: Object, action: Object) {
  return appReducer([SIGN_IN, SIGN_OUT].includes(action.type) ? undefined : state, action);
}
