// @flow
import React from 'react';
import type { FetchedThemeType } from '../types';
import * as user from '../../user';
import Detail from './Detail';
import Names from './Names';
import HttpStar from '../../../components/HttpStar';

type Props = {|
  +theme: FetchedThemeType,
|};
const Header = ({ theme }: Props) => (
  <>
    <div>
      {user.isLoggedIn() && <HttpStar themeId={theme.id} />}
      <Names theme={theme} />
    </div>
    <Detail theme={theme} />
  </>
);

export default Header;
