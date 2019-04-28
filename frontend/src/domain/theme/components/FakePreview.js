// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import FakeLabel from '../../tags/components/FakeLabel';

export default function () {
  return (
    <>
      <Link className="no-link" to="/">
        <h2 style={{ color: '#000000' }}>
          {'.'.repeat(10)}
        </h2>
      </Link>
      <div>
        <small style={{ color: '#000000' }}>
          {'.'.repeat(10)}
        </small>
      </div>
      <FakeLabel>{'.'.repeat(10)}</FakeLabel>
    </>
  );
}
