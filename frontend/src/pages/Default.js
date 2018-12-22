// @flow
import React from 'react';

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +history: Object,
|};
type State = {|
  keyword: string,
|};
export default class extends React.Component<Props, State> {
  state = {
    keyword: '',
  };

  handleChange = ({ target: { name, value } }: TargetType) => {
    this.setState({ [name]: value });
  };

  handleSubmit = (event: { ...TargetType, preventDefault: () => (void) }) => {
    event.preventDefault();
    const { keyword } = this.state;
    if (keyword.trim() !== '') {
      this.props.history.push(`/themes/search?q=${keyword}`);
    }
  };

  render() {
    return (
      <>
        <h1 className="text-center">bulletpoint</h1>
        <img src="images/bulletpoint_logo.svg" className="img-responsive center-block" alt="bulletpoint" title="bulletpoint" width="830" height="210" />
        <div className="row">
          <div className="col-sm-12">
            <form onSubmit={this.handleSubmit}>
              <div className="input-group">
                <input
                  name="keyword"
                  value={this.state.keyword}
                  onChange={this.handleChange}
                  autoFocus
                  className="form-control input-lg"
                  placeholder="Téma..."
                  autoComplete="off"
                />
                <span className="input-group-btn">
                  <button type="submit" className="btn btn-default input-lg">Hledej</button>
                </span>
              </div>
            </form>
          </div>
        </div>
      </>
    );
  }
}
