// @flow
import React from 'react';
import Form from '../../../domain/tags/components/Form/HttpForm';

type Props = {|
  +history: Object,
|};
export default function ({ history }: Props) {
  return (
    <>
      <h1>Přidat tag</h1>
      <Form history={history} />
    </>
  );
}
