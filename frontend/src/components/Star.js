// @flow
import React, { useState } from 'react';
import styled from 'styled-components';
import classNames from 'classnames';

const Resized = styled.span`
  font-size: 34px;
  color: #f2b01e;
  cursor: pointer;
`;

type Props = {|
  +active: boolean,
  +onClick: (boolean) => (Promise<any>),
|};
export default function (props: Props) {
  const [active, setActive] = useState(props.active);

  const mark = () => setActive(true);

  const unmark = () => {
    if (!props.active) {
      setActive(false);
    }
  };

  const handleClick = () => props.onClick(!props.active);

  return (
    <Resized
      onFocus={mark}
      onMouseOver={mark}
      onBlur={unmark}
      onMouseOut={unmark}
      onClick={handleClick}
      className={classNames('glyphicon', active ? 'glyphicon-star' : 'glyphicon-star-empty')}
      aria-hidden="true"
    />
  );
}
