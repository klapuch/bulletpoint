// @flow
import React from 'react';
import type { FetchedThemeType } from '../types';
import Star from '../../../components/Star';
import * as user from '../../user';
import Detail from './Detail';
import Titles from './Titles';

type Props = {|
  +theme: FetchedThemeType,
  +onStarClick: (boolean) => (void),
|};
const Header = ({ theme, onStarClick }: Props) => (
  <>
    <div>
      {user.isLoggedIn() && <Star active={theme.is_starred} onClick={onStarClick} />}
      <Titles theme={theme} />
    </div>
    <Detail theme={theme} />
  </>
);

export default Header;
