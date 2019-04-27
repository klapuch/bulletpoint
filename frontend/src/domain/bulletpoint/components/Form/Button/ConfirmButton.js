// @flow
import React from 'react';
import * as user from '../../../../user';
import SubmitButton from './SubmitButton';
import type { ButtonProps } from '../types';
import { FORM_TYPE_ADD, FORM_TYPE_EDIT } from '../types';

const ConfirmButton = ({ formType, onClick }: ButtonProps) => {
  if (formType === FORM_TYPE_ADD) {
    return (
      <SubmitButton formType={formType} onClick={onClick}>
        {user.isAdmin() ? 'Přidat' : 'Navrhnout'}
      </SubmitButton>
    );
  } else if (formType === FORM_TYPE_EDIT) {
    return <SubmitButton formType={formType} onClick={onClick}>Upravit</SubmitButton>;
  }
  return null;
};

export default ConfirmButton;
