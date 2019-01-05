// @flow
import React from 'react';

type SourceProps = {|
  type: string,
  link: string,
|};
const Source = ({ type, link }: SourceProps) => {
  if (type === 'web') {
    return (
      <>
        <span style={{ marginRight: 4 }} className="glyphicon glyphicon-globe" aria-hidden="true" />
        <a href={link}>{decodeURI(link)}</a>
      </>
    );
  }
  return null;
};

export default Source;
