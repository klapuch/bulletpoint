// @flow
import axios from 'axios';
import { forEach } from 'lodash';
import {
  invalidatedAll,
  receivedAll,
  receivedUpdateSingle,
  requestedAll,
  requestedUpdateSingle,
} from './actions';
import * as theme from '../theme/endpoints';
import * as bulletpoints from './selects';
import type { PostedBulletpointType, PointType } from './types';

export const rate = (
  bulletpoint: number,
  point: PointType,
) => (
  axios.patch(`/bulletpoints/${bulletpoint}`, { rating: { user: point } })
);

export const fetchAll = (
  themeId: number,
) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (bulletpoints.fetchedAll(themeId, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedAll(themeId));
  return axios.get(`/themes/${themeId}/bulletpoints`)
    .then(response => dispatch(receivedAll(themeId, response.data)))
    .then(() => bulletpoints.getByTheme(themeId, getState()))
    .then((themeBulletpoints) => {
      forEach(
        themeBulletpoints,
        themeBulletpoint => (
          [
            ...themeBulletpoint.referenced_theme_id,
            ...themeBulletpoint.compared_theme_id,
          ].forEach(referencedThemeId => (
            dispatch(theme.fetchSingle(referencedThemeId))
          ))
        ),
      );
      return themeBulletpoints;
    });
};

export const add = (
  theme: number,
  bulletpoint: PostedBulletpointType,
) => (dispatch: (mixed) => Object) => (
  axios.post(`/themes/${theme}/bulletpoints`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
);

export const deleteOne = (
  theme: number,
  bulletpoint: number,
) => (dispatch: (mixed) => Object) => {
  axios.delete(`/bulletpoints/${bulletpoint}`)
    .then(() => dispatch(invalidatedAll(theme)));
};

export const edit = (
  theme: number,
  id: number,
  bulletpoint: PostedBulletpointType,
) => (dispatch: (mixed) => Object) => (
  axios.put(`/bulletpoints/${id}`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
);

export const updateSingle = (
  theme: number,
  bulletpoint: number,
) => (dispatch: (mixed) => Object) => {
  requestedUpdateSingle(theme);
  axios.get(`/bulletpoints/${bulletpoint}`)
    .then(response => response.data)
    .then(payload => dispatch(receivedUpdateSingle(payload)));
};
