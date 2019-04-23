// @flow
import axios from 'axios';
import { forEach } from 'lodash';
import { invalidatedAll, receivedAll, requestedAll } from './actions';
import { fetchedAll } from './selects';
import type { PostedBulletpointType } from '../bulletpoint/types';
import * as bulletpoints from '../bulletpoint/selects';
import * as theme from '../theme/endpoints';

export const fetchAll = (
  themeId: number,
) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (fetchedAll(themeId, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedAll(themeId));
  return axios.get(`/themes/${themeId}/contributed_bulletpoints`)
    .then(response => dispatch(receivedAll(themeId, response.data)))
    .then(() => bulletpoints.getByTheme(themeId, getState()))
    .then((themeBulletpoints) => {
      forEach(
        themeBulletpoints,
        themeBulletpoint => (
          themeBulletpoint.referenced_theme_id.forEach(referencedThemeId => (
            dispatch(theme.fetchSingle(referencedThemeId))
          ))
        ),
      );
    });
};

export const add = (
  theme: number,
  bulletpoint: PostedBulletpointType,
) => (dispatch: (mixed) => Object) => (
  axios.post(`/themes/${theme}/contributed_bulletpoints`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
);

export const deleteOne = (
  theme: number,
  bulletpoint: number,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.delete(`/contributed_bulletpoints/${bulletpoint}`)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};
