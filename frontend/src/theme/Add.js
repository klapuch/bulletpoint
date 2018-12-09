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
  +tags: Array<Object>,
|};
type State = {|
  theme: {|
    name: string,
    tags: Array<number>,
    reference: {|
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
        url: '',
      },
    },
  };

  onChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'reference_url') {
      input = { reference: { ...this.state.theme.reference, url: value } };
    } else if (name === 'tags') {
      input = { ...this.state.theme, tags: [parseInt(value, 10)] };
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
        {theme.tags.map(actualTag => (
          <div key={actualTag} className="form-group">
            <label htmlFor="tags">Tag</label>
            <select className="form-control" id="tags" name="tags" value={actualTag} onChange={this.onChange}>
              {this.props.tags.map(tag => <option key={tag.id} value={tag.id}>{tag.name}</option>)}
            </select>
          </div>
        ))}
        <div className="form-group">
          <label htmlFor="reference_url">URL odkazu</label>
          <input type="text" className="form-control" id="reference_url" name="reference_url" value={theme.reference.url} onChange={this.onChange} />
        </div>
        <a href="#" className="btn btn-success" onClick={this.onSubmitClick} role="button">Vytvořit téma</a>
      </form>
    );
  }
}

export default Add;
