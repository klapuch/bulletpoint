import React from 'react';
import { SpaceLink } from './Link';
import type { ButtonProps } from './types';

const CancelButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  if (formType === 'default') {
    return null;
  }
  return (
    <SpaceLink className="btn btn-danger" onClick={onClick} role="button">{children}</SpaceLink>
  );
};

export default CancelButton;
