// @flow
import { merge, pickBy } from 'lodash';
import qs from 'qs';
import * as session from '../domain/access/session';

export default function withSettings(inherited: Object): Object {
  return merge(
    inherited,
    {
      baseURL: process.env.REACT_APP_API,
      maxRedirects: 0,
      timeout: 4000,
      headers: {
        common: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      },
      transformRequest: [...inherited.transformRequest, (data, headers) => {
        const value = session.getValue();
        if (value) {
          headers['Authorization'] = `Bearer ${value}`; // eslint-disable-line
        }
        return data;
      }],
      paramsSerializer: params => qs.stringify(pickBy(params, param => param !== null), { arrayFormat: 'brackets' }),
    },
  );
}
