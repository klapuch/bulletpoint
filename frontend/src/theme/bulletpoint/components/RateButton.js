// @flow
import React from 'react';
import styled from 'styled-components';
import classNames from 'classnames';
import * as session from '../../../access/session';

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
}: { ...Props, type: 'success'|'danger' }) => (
  <RateButton
    className={classNames('badge', 'badge-guest', `alert-${type}`, !session.exists() || rated ? '' : 'opposite-rating')}
    onClick={session.exists() ? onClick : () => null}
  >
    {children}
    <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
  </RateButton>
);

export const UpButton = ({ ...props }: Props) => <Button type="success" {...props} />;
export const DownButton = ({ ...props }: Props) => <Button type="danger" {...props} />;
