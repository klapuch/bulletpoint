// @flow
import React from 'react';
import Center from '../components/Center';

type Props = *;
type State = {|
  +show: boolean,
|};
export default class extends React.PureComponent<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = { show: false };
    this.timer = setTimeout(this.showMessage, 250);
  }

  componentWillUnmount() {
    clearTimeout(this.timer);
  }

  showMessage = () => this.setState({ show: true });

  timer: any;

  render() {
    const { show } = this.state;
    if (show === true) {
      return (
        <Center>
          <h1>Načítám...</h1>
        </Center>
      );
    }
    return null;
  }
}
