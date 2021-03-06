// @flow
import React from 'react';
import classNames from 'classnames';
import type { ErrorBulletpointType, FetchedBulletpointType, PostedBulletpointType } from '../../types';
import * as validation from '../../validation';
import type { FetchedThemeType } from '../../../theme/types';
import CancelButton from './Button/CancelButton';
import ConfirmButton from './Button/ConfirmButton';
import type {
  TargetType,
  FormTypes,
  ReferencedThemesType,
  ComparedThemesType,
} from './types';
import { FORM_TYPE_DEFAULT } from './types';
import ReferencedThemes from './Input/ReferencedThemes';
import ComparedThemes from './Input/ComparedThemes';
import { fromFetchedToPosted } from '../../types';
import PossibleGroups from './Input/PossibleGroups';

type Props = {|
  +bulletpoint?: FetchedBulletpointType,
  // $FlowFixMe ok
  +onSubmit: (PostedBulletpointType) => (void),
  +onCancelClick: () => (void),
  +type: FormTypes,
  +theme: FetchedThemeType,
|};
type State = {|
  referencedThemes: ReferencedThemesType,
  comparedThemes: ComparedThemesType,
  bulletpoint: PostedBulletpointType,
  errors: ErrorBulletpointType,
  bulletpointId: number|null,
|};
const emptyThemeSelection = { id: null, name: null };
const initState = {
  bulletpointId: null,
  referencedThemes: [emptyThemeSelection],
  comparedThemes: [emptyThemeSelection],
  bulletpoint: {
    content: '',
    referenced_theme_id: [],
    compared_theme_id: [],
    source: {
      link: '',
      type: 'web',
    },
    group: {
      root_bulletpoint_id: 0,
    },
  },
  errors: validation.initErrors,
};
export default class extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.reload();
  }

  reload = () => {
    const { bulletpoint, theme } = this.props;
    if (bulletpoint === undefined) {
      this.setState((prevState) => {
        const state = Object.assign({}, prevState);
        state.bulletpoint.source.link = theme.reference.url;
        return state;
      });
    } else {
      const toSelectionFormat = theme => theme
        .filter(Boolean)
        .map(single => ({ id: single.id, name: single.name }));
      if (bulletpoint.group.root_bulletpoint_id === null) {
        bulletpoint.group.root_bulletpoint_id = 0;
      }
      this.setState({
        bulletpointId: bulletpoint.id,
        bulletpoint: fromFetchedToPosted(bulletpoint),
        referencedThemes: [
          ...toSelectionFormat(bulletpoint.referenced_theme),
          emptyThemeSelection,
        ],
        comparedThemes: [
          ...toSelectionFormat(bulletpoint.compared_theme),
          emptyThemeSelection,
        ],
      });
    }
  };

  onChange = ({ target: { name, value } }: TargetType) => {
    const { theme } = this.props;
    let input = null;
    switch (name) {
      case 'source_link':
        input = { source: { ...this.state.bulletpoint.source, link: value } };
        break;
      case 'source_type':
        input = { source: { type: value, link: value === 'web' ? theme.reference.url : null } };
        break;
      case 'group_root_bulletpoint_id':
        input = { group: { root_bulletpoint_id: parseInt(value, 10) } };
        break;
      default:
        input = { [name]: value };
        break;
    }
    this.setState(prevState => ({
      // $FlowFixMe goes from select
      bulletpoint: {
        ...prevState.bulletpoint,
        ...input,
      },
    }));
  };

  handleThemeChange = (
    select: ?Object,
    action: string,
    order: number,
    fetchedName: string,
    currentName: string,
  ) => {
    let { [currentName]: currentTheme, bulletpoint: { [fetchedName]: fetchedTheme } } = this.state;
    if (action === 'clear' && currentTheme.length > 1) {
      delete fetchedTheme[order];
      delete currentTheme[order];
    } else {
      const option = select || { value: 0, label: null };
      fetchedTheme = [...fetchedTheme, option.value];
      currentTheme = [
        ...currentTheme,
        { id: option.value, name: option.label },
      ];
      currentTheme = [
        ...currentTheme.filter(Boolean).filter(theme => theme.id !== null),
        emptyThemeSelection,
      ];
    }
    this.setState(
      prevState => ({
        bulletpoint: {
          ...prevState.bulletpoint,
          [fetchedName]: fetchedTheme.filter(Boolean),
        },
        [currentName]: currentTheme,
      }),
    );
  };

  handleReferencedTheme = (select: ?Object, { action }: {| action: string |}, order: number) => {
    this.handleThemeChange(select, action, order, 'referenced_theme_id', 'referencedThemes');
  };

  handleComparedTheme = (select: ?Object, { action }: {| action: string |}, order: number) => {
    this.handleThemeChange(select, action, order, 'compared_theme_id', 'comparedThemes');
  };

  onSubmit = () => {
    const { bulletpoint } = this.state;
    if (validation.anyErrors(bulletpoint)) {
      this.setState(prevState => ({
        ...prevState,
        errors: validation.errors(prevState.bulletpoint),
      }));
    } else {
      // $FlowFixMe should be ok - null is allowed
      bulletpoint.group.root_bulletpoint_id = bulletpoint.group.root_bulletpoint_id || null;
      this.props.onSubmit(bulletpoint);
    }
  };

  onCancelClick = () => {
    this.props.onCancelClick();
    this.reload();
  };

  render() {
    const { bulletpoint, bulletpointId, errors } = this.state;
    if (this.props.type === FORM_TYPE_DEFAULT) {
      return null;
    }
    return (
      <>
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
          <div className={classNames('form-group', errors.referenced_themes && 'has-error')}>
            <ReferencedThemes
              theme={this.props.theme}
              onSelectChange={this.handleReferencedTheme}
              themes={this.state.referencedThemes}
            />
            {errors.referenced_themes && <span className="help-block">{validation.toMessage(errors, 'referenced_themes')}</span>}
          </div>
          <ComparedThemes
            theme={this.props.theme}
            onSelectChange={this.handleComparedTheme}
            themes={this.state.comparedThemes}
          />
          <PossibleGroups
            themeId={this.props.theme.id}
            onSelectChange={this.onChange}
            bulletpoint={{ id: bulletpointId, ...bulletpoint }}
          />
          <div className="form-group">
            <label htmlFor="source_type">Typ zdroje</label>
            <select className="form-control" id="source_type" name="source_type" value={bulletpoint.source.type} onChange={this.onChange}>
              <option value="web">Web</option>
              <option value="head">Z vlastní hlavy</option>
            </select>
          </div>
          {bulletpoint.source.type !== 'head' && (
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
        <ConfirmButton onClick={this.onSubmit} formType={this.props.type} />
        <CancelButton onClick={this.onCancelClick} formType={this.props.type}>
          Zrušit
        </CancelButton>
      </>
    );
  }
}
