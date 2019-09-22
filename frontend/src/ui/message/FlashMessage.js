// @flow
import React from 'react';
import { connect } from 'react-redux';
import {
  discardedMessage,
  RECEIVED_API_ERROR,
  RECEIVED_SUCCESS,
} from './actions';

type MessageProps = {|
  +children: string,
  +type: 'success'|'danger',
  +onClose: () => (void),
|};
const Message = ({ children, type, onClose }: MessageProps) => (
  <div className={`alert alert-dismissible alert-${type}`}>
    <button
      onClick={onClose}
      type="button"
      className="close"
      aria-label="Close"
    >
      <span aria-hidden="true">&times;</span>
    </button>
    {children}
  </div>
);

type State = {|
  discarded: boolean,
|};
type Props = {|
  +pathname: string,
  +content: string,
  +type: string,
  +discardMessage: () => (void),
|};
class FlashMessage extends React.Component<Props, State> {
  componentDidUpdate(prevProps: Props): void {
    if (this.props.type === RECEIVED_SUCCESS) {
      setTimeout(this.props.discardMessage, 2000);
    } else if (prevProps.pathname !== this.props.pathname) {
      this.props.discardMessage();
    }
  }

  render() {
    const { content, type } = this.props;
    if (content === null) {
      return null;
    }

    switch (type) {
      case RECEIVED_SUCCESS:
        return <Message type="success" onClose={this.props.discardMessage}>{content}</Message>;
      case RECEIVED_API_ERROR:
        return <Message type="danger" onClose={this.props.discardMessage}>{content}</Message>;
      default:
        return null;
    }
  }
}
const mapStateToProps = state => ({
  content: state.message.content,
  type: state.message.type,
});
const mapDispatchToProps = dispatch => ({
  discardMessage: () => dispatch(discardedMessage()),
});
export default connect(mapStateToProps, mapDispatchToProps)(FlashMessage);
