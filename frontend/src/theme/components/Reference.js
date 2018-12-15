// @flow
import React from 'react';

type ReferenceProps = {|
  url: string,
|};
const Reference = ({ url }: ReferenceProps) => {
  return (
    <a href={url} title="Wikipedia">
      <span className="glyphicon glyphicon-link" aria-hidden="true" />
    </a>
  );
};

export default Reference;
