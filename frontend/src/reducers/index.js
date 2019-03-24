// @flow
import { combineReducers } from 'redux';
import theme from '../domain/theme/reducer';
import themeBulletpoints from '../domain/bulletpoint/reducer';
import themeContributedBulletpoints from '../domain/contributed_bulletpoint/reducer';
import tags from '../domain/tags/reducer';
import message from '../ui/message/reducer';
import dataset from '../api/dataset/reducer';
import user from '../domain/user/reducer';
import { SIGN_IN, SIGN_OUT } from '../domain/sign/actions';

const appReducer = combineReducers({
  theme,
  themeBulletpoints,
  themeContributedBulletpoints,
  tags,
  message,
  dataset,
  user,
});

export default function rootReducer(state: Object, action: Object) {
  return appReducer([SIGN_IN, SIGN_OUT].includes(action.type) ? undefined : state, action);
}
