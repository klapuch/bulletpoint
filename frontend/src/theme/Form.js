// @flow
import React from 'react';
import { isEmpty, zipObject } from 'lodash';
import Select from 'react-select';
import type { FetchedThemeType, PostedThemeType } from './types';
import type { TagType } from '../tags/types';

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedThemeType) => (void),
  +tags: Array<TagType>,
  +theme?: ?FetchedThemeType,
|};
type State = {|
  theme: PostedThemeType,
|};
class Form extends React.Component<Props, State> {
  state = {
    theme: {
      name: '',
      tags: [],
      reference: {
        url: '',
      },
    },
  };

  componentDidMount(): void {
    if (!isEmpty(this.props.theme)) {
      // $FlowFixMe Checked via isEmpty
      const { name, tags, reference } = this.props.theme;
      this.setState({
        theme: {
          name,
          tags: tags.map(tag => tag.id),
          reference: {
            url: reference.url,
          },
        },
      });
    }
  }

  handleChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'reference_url') {
      input = { reference: { ...this.state.theme.reference, url: value } };
    } else {
      input = { ...this.state.theme, [name]: value };
    }
    this.setState(prevState => ({
      theme: {
        ...prevState.theme,
        ...input,
      },
    }));
  };

  handleSelectChange = (selects: Array<Object>) => {
    this.setState(prevState => ({
      theme: { ...prevState.theme, tags: selects.map(select => select.value) },
    }));
  };

  handleSubmit = () => this.props.onSubmit(this.state.theme);

  render() {
    const { theme } = this.state;
    const { tags } = this.props;
    const options = zipObject(tags.map(tag => tag.id), tags);
    return (
      <form>
        <div className="form-group">
          <label htmlFor="name">Název</label>
          <input type="text" className="form-control" id="name" name="name" value={theme.name} onChange={this.handleChange} />
        </div>
        <div className="form-group">
          <label htmlFor="tags">Tag</label>
          {/* $FlowFixMe Bad type from lib */}
          <Select
            isMulti
            placeholder="Vyber..."
            value={theme.tags.map(tag => ({ value: tag, label: options[tag].name }))}
            onChange={this.handleSelectChange}
            options={tags.map(tag => ({ value: tag.id, label: tag.name }))}
            styles={{ option: base => ({ ...base, color: '#000' }) }}
          />
        </div>
        <div className="form-group">
          <label htmlFor="reference_url">URL odkazu</label>
          <input type="text" className="form-control" id="reference_url" name="reference_url" value={theme.reference.url} onChange={this.handleChange} />
        </div>
        <a href="#" className="btn btn-success" onClick={this.handleSubmit} role="button">Uložit</a>
      </form>
    );
  }
}

export default Form;
