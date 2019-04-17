// @flow
import React from 'react';
import Form from '../../../domain/theme/components/CreateHttpForm';

type Props = {|
  +history: Object,
|};
export default function (props: Props) {
  return (
    <>
      <h1>Nové téma</h1>
      <Form history={props.history} />
    </>
  );
}
