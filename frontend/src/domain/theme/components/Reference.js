// @flow
import React from 'react';
import type { ReferenceType } from '../types';

type Props = {|
  +children: ReferenceType,
|};
const Reference = ({ children }: Props) => (
  <a
    href={children.url}
    className={children.is_broken ? 'broken_link' : null}
    title={children.is_broken ? 'Odkaz je pravděpodobně poškozen' : null}
  >
    <span className="glyphicon glyphicon-link" aria-hidden="true" />
  </a>
);

export default Reference;
