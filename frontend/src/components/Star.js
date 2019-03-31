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
  +onClick: (boolean) => (Promise<any>),
|};
type State = {|
  active: boolean,
|};
export default class extends React.Component<Props, State> {
  state = {
    active: false,
  };

  componentDidMount(): void {
    this.setState({ active: this.props.active });
  }

  mark = () => {
    this.setState({ active: true });
  };

  unmark = () => {
    if (!this.props.active) {
      this.setState({ active: false });
    }
  };

  render() {
    const { active } = this.state;
    return (
      <Resized
        onFocus={this.mark}
        onMouseOver={this.mark}
        onBlur={this.unmark}
        onMouseOut={this.unmark}
        onClick={
          () => this.props.onClick(!this.props.active)
            .then(() => this.setState({ active: !this.props.active }))
        }
        className={classNames('glyphicon', active ? 'glyphicon-star' : 'glyphicon-star-empty')}
        aria-hidden="true"
      />
    );
  }
}
