// @flow
import getSlug from 'speakingurl';
import pathToRegexp from 'path-to-regexp';
import { Redirect } from 'react-router-dom';
import React from 'react';

const withSlug = (match: Object, name: string): void => {
  const toPath = pathToRegexp.compile(match.path);
  return toPath({ ...match.params, slug: getSlug(name) });
};

const differs = (match: Object, name: string): boolean => (
  Object.prototype.hasOwnProperty.call(match.params, 'slug') && (getSlug(name) !== match.params.slug)
);

type Props = {
  children: *,
  match: Object,
  name: string,
};
export default class extends React.PureComponent<Props> {
  render() {
    const { match, name, children } = this.props;
    if (differs(match, name)) {
      return <Redirect to={withSlug(match, name)} />;
    }
    return children;
  }
}
