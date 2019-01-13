// @flow

import React from 'react';
import styled from 'styled-components';
import classNames from 'classnames';
import * as user from '../domain/user';

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
export default class Star extends React.PureComponent<Props, State> {
  state = {
    active: false,
  };

  componentDidMount(): void {
    this.setState({ active: this.props.active });
  }

  mark = () => {
    if (this.props.active) {
      this.setState({ active: true });
    } else {
      this.setState((prevState: State) => ({ active: !prevState.active }));
    }
  };

  render() {
    if (!user.isLoggedIn()) {
      return null;
    }
    const { active } = this.state;
    return (
      <Resized
        onFocus={this.mark}
        onBlur={this.mark}
        onClick={() => this.props.onClick(!this.props.active)}
        className={classNames('glyphicon', active ? 'glyphicon-star' : 'glyphicon-star-empty')}
        aria-hidden="true"
      />
    );
  }
}
