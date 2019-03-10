// @flow
import React from 'react';
import * as user from '../../../user';
import SubmitButton from './SubmitButton';
import { FORM_TYPE_DEFAULT } from './types';

type Props = {|
  onClick: () => (void),
|};
const AddButton = ({ onClick }: Props) => (
  <SubmitButton formType={FORM_TYPE_DEFAULT} onClick={onClick}>
    {user.isAdmin() ? 'Přidat bulletpoint' : 'Navrhnout bulletpoint'}
  </SubmitButton>
);

export default AddButton;
