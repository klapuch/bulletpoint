import classNames from 'classnames';
import React from 'react';
import { SpaceLink } from './Link';
import type { ButtonProps } from '../types';
import { FORM_TYPE_DEFAULT } from '../types';

const SubmitButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  const className = classNames('btn', formType === FORM_TYPE_DEFAULT ? 'btn-default' : 'btn-success');
  return (
    <SpaceLink className={className} onClick={onClick} role="button">{children}</SpaceLink>
  );
};

export default SubmitButton;
