// @flow
import React from 'react';
import Select from 'react-select';
import type { OptionType } from 'react-select/src/types';
import type { ThemeType } from './endpoints';

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
  theme: { ...ThemeType, tags: Array<OptionType> },
|};
class Add extends React.Component<Props, State> {
  state = {
    theme: {
      name: '',
      tags: [],
      reference: {
        url: '',
      },
    },
  };

  handleFormChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'reference_url') {
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

  handleSelectChange = (selects: Array<Object>) => {
    this.setState(prevState => ({
      ...prevState,
      theme: {
        ...prevState.theme,
        tags: selects.map(select => select.value),
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
          <input type="text" className="form-control" id="name" name="name" value={theme.name} onChange={this.handleFormChange} />
        </div>
        <div className="form-group">
          <label htmlFor="tags">Tag</label>
          <Select
            isMulti
            placeholder="Vyber..."
            defaultValue={theme.tags}
            onChange={this.handleSelectChange}
            options={this.props.tags.map(tag => ({ value: tag.id, label: tag.name }))}
            styles={{ option: base => ({ ...base, color: '#000' }) }}
          />
        </div>
        <div className="form-group">
          <label htmlFor="reference_url">URL odkazu</label>
          <input type="text" className="form-control" id="reference_url" name="reference_url" value={theme.reference.url} onChange={this.handleFormChange} />
        </div>
        <a href="#" className="btn btn-success" onClick={this.onSubmitClick} role="button">Vytvořit téma</a>
      </form>
    );
  }
}

export default Add;
