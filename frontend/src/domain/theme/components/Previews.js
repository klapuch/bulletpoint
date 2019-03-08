// @flow
import React from 'react';
import type { FetchedThemeType } from '../types';
import Preview from './Preview';

type Props = {|
  +themes: Array<FetchedThemeType>,
|};
const Previews = ({ themes }: Props) => (
  // $FlowFixMe Not sure why
  themes.map(theme => (
    <React.Fragment key={theme.id}>
      <Preview>{theme}</Preview>
      <hr />
    </React.Fragment>
  ))
);

export default Previews;
