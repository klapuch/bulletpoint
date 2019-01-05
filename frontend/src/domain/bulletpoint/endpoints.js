// @flow
import axios from 'axios';
import { forEach } from 'lodash';
import {
  invalidatedAll,
  receivedAll,
  receivedUpdateSingle,
  requestedAll,
  requestedUpdateSingle,
  requestedExtendSingle,
  receivedExtendSingle,
} from './actions';
import * as theme from '../theme/endpoints';
import * as themes from '../theme/selects';
import * as bulletpoints from './selects';
import type { FetchedBulletpointType, PostedBulletpointType } from './types';

export const all = (theme: number) => (dispatch: (mixed) => Object, getState: () => Object) => {
  if (bulletpoints.fetchedAll(theme, getState())) {
    return Promise.resolve();
  }
  dispatch(requestedAll(theme));
  return axios.get(`/themes/${theme}/bulletpoints`)
    .then(response => dispatch(receivedAll(theme, response.data)));
};

export const allWithReferencedThemes = (
  themeId: number,
) => (dispatch: (mixed) => Object, getState: () => Object) => {
  dispatch(all(themeId))
    .then(() => bulletpoints.getByTheme(themeId, getState()))
    .then(themeBulletpoints => {
      forEach(
        themeBulletpoints.filter(themeBulletpoint => themeBulletpoint.referenced_theme_id !== null),
        themeBulletpoint => (
          dispatch(theme.single(themeBulletpoint.referenced_theme_id))
        ),
      )
    })
};

export const add = (
  theme: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => (
  axios.post(`/themes/${theme}/bulletpoints`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next)
);

export const deleteOne = (
  theme: number,
  bulletpoint: number,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => {
  axios.delete(`/bulletpoints/${bulletpoint}`)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next);
};

export const edit = (
  theme: number,
  id: number,
  bulletpoint: PostedBulletpointType,
  next: (void) => (void),
) => (dispatch: (mixed) => Object) => (
  axios.put(`/bulletpoints/${id}`, bulletpoint)
    .then(() => dispatch(invalidatedAll(theme)))
    .then(next)
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
