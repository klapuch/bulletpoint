// @flow
import React from 'react';
import { Route, Link } from 'react-router-dom';

type Props = {|
  +children: string,
  +title?: string,
  +to: string,
  +exact?: boolean,
|};
export default function NavItem({
  children, title, to, exact,
}: Props) {
  return (
    <Route
      path={to}
      exact={exact}
      children={({ match }) => ( // eslint-disable-line
        <li title={title} className={match && 'active'}>
          <Link to={to}>{children}</Link>
        </li>
      )}
    />
  );
}
