// @flow
import React from 'react';

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +onSubmit: (Object) => (void),
|};
type State = {|
  theme: {|
    name: string,
    tags: Array<number>,
    reference: {|
      name: string,
      url: string,
    |}
  |},
|};
class Add extends React.Component<Props, State> {
  state = {
    theme: {
      name: '',
      tags: [1],
      reference: {
        name: 'wikipedia',
        url: '',
      },
    },
  };

  onChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'reference_name') {
      input = { reference: { ...this.state.theme.reference, name: value } };
    } else if (name === 'reference_url') {
      input = { reference: { ...this.state.theme.reference, url: value } };
    } else {
      input = { ...this.state.theme, [name]: value };
    }
    this.setState(prevState => ({
      ...prevState,
      theme: {
        ...prevState.theme,
        ...input,
      },
    }));
  };

  onSubmitClick = () => {
    this.props.onSubmit(this.state.theme);
  };

  render() {
    const { theme } = this.state;
    return (
      <form>
        <div className="form-group">
          <label htmlFor="name">Název</label>
          <input type="text" className="form-control" id="name" name="name" value={theme.name} onChange={this.onChange} />
        </div>
        {theme.tags.map(tag => (
          <div className="form-group">
            <label htmlFor="tag">Tag</label>
            <select className="form-control" id="tag" name="tag" value={tag} onChange={this.onChange}>
              <option value={1}>IT</option>
              <option value={2}>Programming language</option>
            </select>
          </div>
        ))}
        <div className="form-group">
          <label htmlFor="reference_type">Název odkazu</label>
          <select className="form-control" id="reference_name" name="reference_name" value={theme.reference.name} onChange={this.onChange}>
            <option value="wikipedia">Wikipedia</option>
          </select>
          <label htmlFor="reference_url">URL odkazu</label>
          <input type="text" className="form-control" id="reference_url" name="reference_url" value={theme.reference.url} onChange={this.onChange} />
        </div>
        <a href="#" className="btn btn-success" onClick={this.onSubmitClick} role="button">Vytvořit téma</a>
      </form>
    );
  }
}

export default Add;
