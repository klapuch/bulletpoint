// @flow
import React from 'react';
import type { FetchedThemeType } from './types';
import Single from './Single';

type Props = {|
  +themes: Array<FetchedThemeType>,
|};
const All = ({ themes }: Props) => (
  // $FlowFixMe Not sure why
  themes.map(theme => (
    <React.Fragment key={theme.id}>
      <Single tags={theme.tags} id={theme.id}>{theme.name}</Single>
      <hr />
    </React.Fragment>
  ))
);

export default All;
