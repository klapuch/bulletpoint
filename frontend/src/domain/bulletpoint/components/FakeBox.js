// @flow
import React from 'react';
import styled from 'styled-components';

const FakeBox = styled.li`
  height: 62px;
`;

export default function () {
  return (
    <FakeBox className="list-group-item" />
  );
}
