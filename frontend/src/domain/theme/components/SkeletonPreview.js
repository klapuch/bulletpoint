// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import SkeletonLabel from '../../tags/components/SkeletonLabel';

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
      <SkeletonLabel>{'.'.repeat(10)}</SkeletonLabel>
    </>
  );
}
