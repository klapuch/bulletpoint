// @flow
import React from 'react';
import type { FetchedThemeType } from '../types';
import Preview from './Preview';

type Props = {|
  +themes: Array<FetchedThemeType>,
  +tagLink: (number, string) => string,
|};
const Previews = ({ themes, tagLink }: Props) => (
  // $FlowFixMe Not sure why
  themes.map(theme => (
    <React.Fragment key={theme.id}>
      <Preview tagLink={tagLink}>{theme}</Preview>
      <hr />
    </React.Fragment>
  ))
);

export default Previews;
