// @flow
import React from 'react';

type TargetType = {|
  target: {|
    name: string,
    files: Array<*>,
  |},
|};

type Props = {|
  +onSubmit: (FormData) => (void),
|};
type State = {|
  avatar: string,
|};
class Form extends React.Component<Props, State> {
  state = {
    avatar: '',
  };

  handleChange = ({ target: { name, files } }: TargetType) => {
    this.setState({ [name]: files[0] });
  };

  handleSubmit = () => {
    const formData = new FormData();
    formData.append('avatar', this.state.avatar);
    this.props.onSubmit(this.state.avatar);
  };

  render() {
    const { avatar } = this.state;
    return (
      <form className="form-horizontal">
        <div className="form-group">
          <label className="btn btn-default">
            <input type="file" name="avatar" hidden onChange={this.handleChange} />
          </label>
        </div>
        {avatar && (
          <div className="form-group">
            <button type="button" onClick={this.handleSubmit} className="btn btn-success">
              Nahrát
            </button>
          </div>)}
      </form>
    );
  }
}

export default Form;
