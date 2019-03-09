// $FlowFixMe ok
import AsyncSelect from 'react-select/lib/Async';
import React from 'react';
import { allReactSelectTagSearches } from '../../../../theme/endpoints';
import type { ComparedThemesType } from '../types';
import type { FetchedThemeType } from '../../../../theme/types';

type Props = {|
  +theme: FetchedThemeType,
  +onSelectChange: (?Object, Object, number) => (void),
  +themes: ComparedThemesType,
|};
const ComparedThemes = ({ theme: sourceTheme, onSelectChange, themes }: Props) => (
  <div className="form-group">
    <label htmlFor="compared_theme_id">Témata k porovnání</label>
    {themes.map((theme, i) => (
      <div key={i}>
        <label>Téma k porovnání</label>
        <AsyncSelect
          isClearable
          value={{ value: theme.id, label: theme.name }}
          onChange={(select, options) => onSelectChange(select, options, i)}
          loadOptions={keyword => allReactSelectTagSearches(
            keyword,
            sourceTheme.tags,
            [sourceTheme.id],
          )}
          styles={{ option: base => ({ ...base, color: '#000' }) }}
        />
      </div>
    ))}
  </div>
);
export default ComparedThemes;
