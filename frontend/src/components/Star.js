// @flow

import React from 'react';
import styled from 'styled-components';
import classNames from 'classnames';

const Resized = styled.span`
  font-size: 34px;
  color: #f2b01e;
  cursor: pointer;
`;

type Props = {|
  +active: boolean,
  +onClick: (boolean) => (void),
|};
type State = {|
  active: boolean,
|};
export default class Star extends React.Component<Props, State> {
  state = {
    active: false,
  };

  componentDidMount(): void {
    this.setState({ active: this.props.active });
  }

  mark = () => {
    if (!this.props.active) {
      this.setState((prevState: State) => ({ active: !prevState.active }));
    }
  };

  render() {
    const { active } = this.state;
    return (
      <Resized
        onFocus={this.mark}
        onMouseOver={this.mark}
        onBlur={this.mark}
        onMouseOut={this.mark}
        onClick={() => this.props.onClick(!this.props.active)}
        className={classNames('glyphicon', active ? 'glyphicon-star' : 'glyphicon-star-empty')}
        aria-hidden="true"
      />
    );
  }
}
