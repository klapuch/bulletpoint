// @flow
import React from 'react';
import styled from 'styled-components';
import classNames from 'classnames';
import * as user from '../../../user';

const RateButton = styled.span`
  cursor: pointer;
`;

type Props = {|
  +children: number,
  +onClick: (void) => (void),
  +rated: boolean,
|};

const Button = ({
  children, onClick, rated, type,
}: { ...Props, type: 'success' | 'danger' }) => (
  <RateButton
    className={classNames('badge', 'badge-guest', `alert-${type}`, !user.isLoggedIn() || rated ? '' : 'opposite-rating')}
    onClick={user.isLoggedIn() ? onClick : () => null}
  >
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);

export const UpButton = ({ ...props }: Props) => <Button type="success" {...props} />;
export const DownButton = ({ ...props }: Props) => <Button type="danger" {...props} />;
