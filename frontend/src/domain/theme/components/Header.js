// @flow
import React from 'react';
import type { FetchedThemeType } from '../types';
import Star from '../../../components/Star';
import * as user from '../../user';
import Detail from './Detail';
import Names from './Names';

type Props = {|
  +theme: FetchedThemeType,
  +onStarClick: (boolean) => (Promise<any>),
|};
const Header = ({ theme, onStarClick }: Props) => (
  <>
    <div>
      {user.isLoggedIn() && <Star active={theme.is_starred} onClick={onStarClick} />}
      <Names theme={theme} />
    </div>
    <Detail theme={theme} />
  </>
);

export default Header;
