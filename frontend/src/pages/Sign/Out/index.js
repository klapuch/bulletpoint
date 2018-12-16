// @flow
import React from 'react';
import { signOut } from '../../../sign/endpoints';

export default class extends React.PureComponent<*> {
  componentWillMount() {
    signOut(() => window.location.replace('/sign/in'));
  }

  render() {
    return null;
  }
}
