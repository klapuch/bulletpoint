// @flow
import React from 'react';
import styled from 'styled-components';

const SkeletonBox = styled.li`
  height: 62px;
`;

export default function () {
  return (
    <SkeletonBox className="list-group-item" />
  );
}
