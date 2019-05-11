// @flow
import React from 'react';
import Center from '../components/Center';

type Props = {|
  +children: string,
|};
export default function ({ children }: Props) {
  return (
    <Center>
      <h2>{children}</h2>
    </Center>
  );
}
