// @flow
import React from 'react';
import { zipObject } from 'lodash';
// $FlowFixMe temporary - https://github.com/facebook/flow/issues/869
import Select from 'react-select';
import classNames from 'classnames';
import type { ErrorThemeType, FetchedThemeType, PostedThemeType } from '../../types';
import type { FetchedTagType } from '../../../tags/types';
import * as validation from '../../validation';
import { fromFetchedToPosted } from '../../types';

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedThemeType) => (void),
  +tags: Array<FetchedTagType>,
  +theme?: ?FetchedThemeType,
|};
type State = {|
  theme: PostedThemeType,
  errors: ErrorThemeType,
|};
const initStateErrors = {
  name: null,
  tags: null,
  reference_url: null,
};
const initState = {
  theme: {
    name: '',
    alternative_names: [],
    tags: [],
    reference: {
      url: '',
    },
  },
  errors: initStateErrors,
};
class DefaultForm extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    const { theme } = this.props;
    if (theme !== undefined && theme !== null) {
      this.setState({ theme: fromFetchedToPosted(theme) });
    }
  }

  handleChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    switch (name) {
      case 'reference_url':
        input = { reference: { url: value } };
        break;
      case 'alternative_names':
        input = { alternative_names: value.split(',') };
        break;
      default:
        input = { [name]: value };
        break;
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

  handleSubmit = () => {
    const { theme } = this.state;
    if (validation.anyErrors(theme)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.theme),
      }));
    } else {
      this.props.onSubmit(theme);
    }
  };

  render() {
    const { theme, errors } = this.state;
    const { tags } = this.props;
    const options = zipObject(tags.map(tag => tag.id), tags);
    return (
      <form>
        <div className={classNames('form-group', errors.name && 'has-error')}>
          <label htmlFor="name">Název</label>
          <input type="text" className="form-control" id="name" name="name" value={theme.name} onChange={this.handleChange} />
          {errors.name && <span className="help-block">{validation.toMessage(errors, 'name')}</span>}
        </div>
        <div className="form-group">
          <label htmlFor="name">Alternativní názvy</label>
          <input type="text" className="form-control" id="alternative_names" name="alternative_names" value={theme.alternative_names.join(',')} onChange={this.handleChange} />
        </div>
        <div className={classNames('form-group', errors.tags && 'has-error')}>
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
          {errors.tags && <span className="help-block">{validation.toMessage(errors, 'tags')}</span>}
        </div>
        <div className={classNames('form-group', errors.reference_url && 'has-error')}>
          <label htmlFor="reference_url">URL odkazu</label>
          <input type="text" className="form-control" id="reference_url" name="reference_url" value={theme.reference.url} onChange={this.handleChange} />
          {errors.reference_url && <span className="help-block">{validation.toMessage(errors, 'reference_url')}</span>}
        </div>
        <a href="#" className="btn btn-success" onClick={this.handleSubmit} role="button">Uložit</a>
      </form>
    );
  }
}

export default DefaultForm;
