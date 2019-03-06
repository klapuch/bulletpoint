// @flow
import React from 'react';
import classNames from 'classnames';
import type { ErrorBulletpointType, PostedBulletpointType } from '../../types';
import * as validation from '../../validation';
import type { FetchedThemeType } from '../../../theme/types';
import CancelButton from './CancelButton';
import ConfirmButton from './ConfirmButton';
import type {TargetType, FormTypes, ReferencedThemesType, ComparedThemesType} from './types';
import ReferencedThemes from './Input/ReferencedThemes';
import ComparedThemes from './Input/ComparedThemes';
import { FORM_TYPE_DEFAULT } from './types';

type Props = {|
  +bulletpoint: ?PostedBulletpointType,
  +onSubmit: (PostedBulletpointType) => (Promise<any>),
  +onAddClick: () => (void),
  +onCancelClick: () => (void),
  +type: FormTypes,
  +themeId: number,
  +referencedThemes: Array<FetchedThemeType>,
  +comparedThemes: Array<FetchedThemeType>,
|};
type State = {|
  referencedThemes: ReferencedThemesType,
  comparedThemes: ComparedThemesType,
  bulletpoint: PostedBulletpointType,
  errors: ErrorBulletpointType,
|};
const emptyReferencedTheme = { id: null, name: null };
const emptyComparedTheme = { id: null, name: null };
const initState = {
  referencedThemes: [emptyReferencedTheme],
  comparedThemes: [emptyComparedTheme],
  bulletpoint: {
    content: '',
    referenced_theme_id: [],
    compared_theme_id: [],
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
    this.setState(initState);
    if (nextProps.bulletpoint !== null) {
      this.setState(prevState => ({
        // $FlowFixMe its ok
        bulletpoint: nextProps.bulletpoint,
        referencedThemes: [
          ...nextProps.referencedThemes
            .filter(Boolean)
            .map(theme => ({ id: theme.id, name: theme.name })),
          ...prevState.referencedThemes.filter(Boolean),
        ],
        comparedThemes: [
          ...nextProps.comparedThemes
            .filter(Boolean)
            .map(theme => ({ id: theme.id, name: theme.name })),
          ...prevState.comparedThemes.filter(Boolean),
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
      input = { [name]: value };
    }
    this.setState(prevState => ({
      // $FlowFixMe goes from select
      bulletpoint: {
        ...prevState.bulletpoint,
        ...input,
      },
    }));
  };

  handleReferencedTheme = (select: ?Object, { action }: {| action: string |}, order: number) => {
    let { bulletpoint: { referenced_theme_id }, referencedThemes } = this.state;
    if (action === 'clear' && referencedThemes.length > 1) {
      delete referenced_theme_id[order];
      delete referencedThemes[order];
    } else {
      const option = select || { value: 0, label: null };
      referenced_theme_id = [...referenced_theme_id, option.value];
      referencedThemes = [
        ...referencedThemes,
        { id: option.value, name: option.label },
      ];
      referencedThemes = [
        ...referencedThemes.filter(theme => theme.id !== null),
        emptyReferencedTheme,
      ];
    }
    this.setState(
      prevState => ({
        bulletpoint: {
          ...prevState.bulletpoint,
          referenced_theme_id: referenced_theme_id.filter(Boolean),
        },
        referencedThemes,
      }),
    );
  };

  handleComparedTheme = (select: ?Object, { action }: {| action: string |}, order: number) => {
    let { bulletpoint: { compared_theme_id }, comparedThemes } = this.state;
    if (action === 'clear' && comparedThemes.length > 1) {
      delete compared_theme_id[order];
      delete comparedThemes[order];
    } else {
      const option = select || { value: 0, label: null };
      compared_theme_id = [...compared_theme_id, option.value];
      comparedThemes = [
        ...comparedThemes,
        { id: option.value, name: option.label },
      ];
      comparedThemes = [
        ...comparedThemes.filter(theme => theme.id !== null),
        emptyComparedTheme,
      ];
    }
    this.setState(
      prevState => ({
        bulletpoint: {
          ...prevState.bulletpoint,
          compared_theme_id: compared_theme_id.filter(Boolean),
        },
        comparedThemes,
      }),
    );
  };

  onSubmit = () => {
    const { bulletpoint } = this.state;
    if (this.props.type !== FORM_TYPE_DEFAULT && validation.anyErrors(bulletpoint)) {
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
    const { bulletpoint, errors, referencedThemes, comparedThemes } = this.state;
    return (
      <>
        {this.props.type === FORM_TYPE_DEFAULT ? null : (
          <form>
            <div className={classNames('form-group', errors.content && 'has-error')}>
              <label htmlFor="content">Obsah</label>
              <input
                type="text"
                className="form-control"
                id="content"
                name="content"
                value={bulletpoint.content}
                onChange={this.onChange}
              />
              {errors.content && <span className="help-block">{validation.toMessage(errors, 'content')}</span>}
            </div>
            <ReferencedThemes
              id={this.props.themeId}
              onSelectChange={this.handleReferencedTheme}
              themes={referencedThemes}
            />
            <ComparedThemes
              id={this.props.themeId}
              onSelectChange={this.handleComparedTheme}
              themes={comparedThemes}
            />
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
                <input
                  type="text"
                  className="form-control"
                  id="source_link"
                  name="source_link"
                  value={bulletpoint.source.link}
                  onChange={this.onChange}
                />
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
