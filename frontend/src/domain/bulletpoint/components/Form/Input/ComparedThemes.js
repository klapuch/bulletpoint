// $FlowFixMe ok
import AsyncSelect from 'react-select/lib/Async';
import React from 'react';
import { allReactSelectSearches } from '../../../../theme/endpoints';
import type { ComparedThemesType } from '../types';

type Props = {|
  +id: number,
  +onSelectChange: (?Object, Object, number) => (void),
  +themes: ComparedThemesType,
|};
const ComparedThemes = ({ id, onSelectChange, themes }: Props) => (
  <div className="form-group">
    <label htmlFor="compared_theme_id">Témata k porovnání</label>
    {themes.map((theme, i) => (
      <div key={i}>
        <label>Téma k porovnání</label>
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
export default ComparedThemes;
