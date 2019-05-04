// @flow
import React from 'react';
import type { SourceType } from '../../../types';

type Props = {|
  +children: SourceType,
|};
const Source = ({ children }: Props) => {
  if (children.type === 'web') {
    return (
      <>
        <span style={{ marginRight: 4 }} className="glyphicon glyphicon-globe" aria-hidden="true" />
        <a
          href={children.link}
          className={children.is_broken ? 'broken_link' : null}
          title={children.is_broken ? 'Odkaz je pravděpodobně poškozen' : null}
        >{decodeURI(children.link)}
        </a>
      </>
    );
  }
  return null;
};

export default Source;
