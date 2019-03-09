// @flow
import React from 'react';

type ReferenceProps = {|
  url: string,
|};
const Reference = ({ url }: ReferenceProps) => (
  <a href={url}>
    <span className="glyphicon glyphicon-link" aria-hidden="true" />
  </a>
);

export default Reference;
