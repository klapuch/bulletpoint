// @flow
import React from 'react';
// $FlowFixMe ok
import AsyncSelect from 'react-select/lib/Async';
import classNames from 'classnames';
import styled from 'styled-components';
import { allReactSelectSearches } from '../../theme/endpoints';
import type { ErrorBulletpointType, PostedBulletpointType } from '../types';
import * as validation from '../validation';
import * as user from '../../user';
import * as formats from '../formats';
import type {FetchedThemeType} from "../../theme/types";

export type FormTypes = 'default' | 'edit' | 'add';
type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};
type ButtonProps = {|
  formType: FormTypes,
  onClick: () => (void),
|};


const SpaceLink = styled.a`
  margin-right: 5px;
`;


const SubmitButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  const className = classNames('btn', formType === 'default' ? 'btn-default' : 'btn-success');
  return (
    <SpaceLink className={className} onClick={onClick} role="button">{children}</SpaceLink>
  );
};

const ConfirmButton = ({ formType, onClick }: ButtonProps) => {
  if (formType === 'default') {
    return (
      <SubmitButton formType={formType} onClick={onClick}>
        {user.isAdmin() ? 'Přidat bulletpoint' : 'Navrhnout bulletpoint'}
      </SubmitButton>
    );
  } else if (formType === 'add') {
    return (
      <SubmitButton formType={formType} onClick={onClick}>
        {user.isAdmin() ? 'Přidat' : 'Navrhnout'}
      </SubmitButton>
    );
  } else if (formType === 'edit') {
    return <SubmitButton formType={formType} onClick={onClick}>Upravit</SubmitButton>;
  }
  return null;
};

const CancelButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  if (formType === 'default') {
    return null;
  }
  return (
    <SpaceLink className="btn btn-danger" onClick={onClick} role="button">{children}</SpaceLink>
  );
};


type Props = {|
  +bulletpoint: ?PostedBulletpointType,
  +onSubmit: (PostedBulletpointType) => (Promise<any>),
  +onAddClick: () => (void),
  +onCancelClick: () => (void),
  +type: FormTypes,
  +themeId: number,
  +referencedThemes: Array<FetchedThemeType>,
|};
type State = {|
  passedReferencedThemes: Array<{
    id: ?number,
    name: ?string,
  }>,
  preparedReferencedThemes: Array<{
    id: ?number,
    name: ?string,
  }>,
  bulletpoint: PostedBulletpointType,
  errors: ErrorBulletpointType,
|};
const emptyPreparedReferencedTheme = { id: null, name: null };
const initState = {
  referencedThemes: [],
  preparedReferencedThemes: [emptyPreparedReferencedTheme],
  bulletpoint: {
    content: '',
    referenced_theme_id: [],
    source: {
      link: '',
      type: 'web',
    },
  },
  errors: {
    content: null,
    source_link: null,
    source_type: null,
  },
};
export default class extends React.Component<Props, State> {
  state = initState;

  componentWillReceiveProps(nextProps: Props): void {
    if (nextProps.bulletpoint !== null) {
      this.setState(prevState => ({
        bulletpoint: nextProps.bulletpoint,
        preparedReferencedThemes: [
          ...nextProps.referencedThemes.map(theme => ({ id: theme.id, name: theme.name })),
          ...prevState.preparedReferencedThemes,
        ],
      }));
    }
  }

  onChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'source_link') {
      input = { source: { ...this.state.bulletpoint.source, link: value } };
    } else if (name === 'source_type') {
      input = { source: { type: value, link: '' } };
    } else {
      input = { ...this.state.bulletpoint, [name]: value };
    }
    this.setState(prevState => ({
      // $FlowFixMe goes from select
      bulletpoint: {
        ...prevState.bulletpoint,
        ...input,
      },
    }));
  };

  handleSelectChange = (select: ?Object, { action }, order) => {
    let referenced_theme_id = this.state.bulletpoint.referenced_theme_id;
    let preparedReferencedThemes = this.state.preparedReferencedThemes;
    if (action === 'clear' && preparedReferencedThemes.length > 1) {
      delete referenced_theme_id[order];
      delete preparedReferencedThemes[order];
    } else {
      const option = select || { value: null, label: null };
      referenced_theme_id = [option.value, ...referenced_theme_id];
      preparedReferencedThemes = [ ...preparedReferencedThemes, { id: option.value, name: option.label }];
      preparedReferencedThemes = [ ...preparedReferencedThemes.filter(theme => theme.id !== null), emptyPreparedReferencedTheme ];
    }
    this.setState(
      prevState => ({
        bulletpoint: {
          ...prevState.bulletpoint,
          referenced_theme_id,
        },
        preparedReferencedThemes,
      })
    );

  };

  onSubmit = () => {
    const { bulletpoint } = this.state;
    if (this.props.type !== 'default' && validation.anyErrors(bulletpoint)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.bulletpoint),
      }));
    } else {
      this.props.onAddClick();
      this.props.onSubmit(bulletpoint).then(() => this.setState(initState));
    }
  };

  onCancelClick = () => {
    this.props.onCancelClick();
    this.setState(initState);
  };

  render() {
    const { bulletpoint, errors } = this.state;
    const { preparedReferencedThemes } = this.state;
    return (
      <>
        {this.props.type === 'default' ? null : (
          <form>
            <div className={classNames('form-group', errors.content && 'has-error')}>
              <label htmlFor="content">Obsah</label>
              <input type="text" className="form-control" id="content" name="content" value={bulletpoint.content} onChange={this.onChange} />
              {errors.content && <span className="help-block">{validation.toMessage(errors, 'content')}</span>}
            </div>
            <PreparedThemes id={this.props.themeId} onSelectChange={this.handleSelectChange} themes={preparedReferencedThemes} />
            <div className="form-group">
              <label htmlFor="source_type">Typ zdroje</label>
              <select className="form-control" id="source_type" name="source_type" value={bulletpoint.source.type} onChange={this.onChange}>
                <option value="web">Web</option>
                <option value="head">Z vlastní hlavy</option>
              </select>
            </div>
            {bulletpoint.source.type === 'head' ? null : (
              <div className={classNames('form-group', errors.source_link && 'has-error')}>
                <label htmlFor="source_link">Odkaz na zdroj</label>
                <input type="text" className="form-control" id="source_link" name="source_link" value={bulletpoint.source.link} onChange={this.onChange} />
                {errors.source_link && <span className="help-block">{validation.toMessage(errors, 'source_link')}</span>}
              </div>
            )}
          </form>
        )}
        <ConfirmButton onClick={this.onSubmit} formType={this.props.type} />
        <CancelButton onClick={this.onCancelClick} formType={this.props.type}>
          Zrušit
        </CancelButton>
      </>
    );
  }
}

const PreparedThemes = ({ id, onSelectChange, themes }) => (
  <div className="form-group">
    <label htmlFor="referenced_theme_id">Odkazující se témata</label>
    {themes.map((theme, i) => (
      <div key={i}>
        <label>Odkazujcí se téma</label>
        <AsyncSelect
          isClearable
          value={{ value: theme.id, label: theme.name }}
          onChange={(select, options) => onSelectChange(select, options, i)}
          loadOptions={keyword => allReactSelectSearches(keyword, [id])}
          styles={{ option: base => ({ ...base, color: '#000' }) }}
        />
      </div>
    ))}
  </div>
);
