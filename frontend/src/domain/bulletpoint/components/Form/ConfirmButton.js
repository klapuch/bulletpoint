// @flow
import React from 'react';
import * as user from '../../../user';
import SubmitButton from './SubmitButton';
import type { ButtonProps } from './types';

const ConfirmButton = ({ formType, onClick }: ButtonProps) => {
  if (formType === 'add') {
    return (
      <SubmitButton formType={formType} onClick={onClick}>
        {user.isAdmin() ? 'Přidat' : 'Navrhnout'}
      </SubmitButton>
    );
  } else if (formType === 'edit') {
    return <SubmitButton formType={formType} onClick={onClick}>Upravit</SubmitButton>;
  }
  return null;
};

export default ConfirmButton;
