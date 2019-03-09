// $FlowFixMe ok
import AsyncSelect from 'react-select/lib/Async';
import React from 'react';
import { allReactSelectSearches } from '../../../../theme/endpoints';
import type { ReferencedThemesType } from '../types';
import type { FetchedThemeType } from '../../../../theme/types';

type Props = {|
  +theme: FetchedThemeType,
  +onSelectChange: (?Object, Object, number) => (void),
  +themes: ReferencedThemesType,
|};
const ReferencedThemes = ({ theme: sourceTheme, onSelectChange, themes }: Props) => (
  <>
    <label htmlFor="referenced_theme_id">Odkazující se témata</label>
    {themes.map((theme, i) => (
      <div key={i}>
        <label>Odkazujcí se téma</label>
        <AsyncSelect
          isClearable
          value={{ value: theme.id, label: theme.name }}
          onChange={(select, options) => onSelectChange(select, options, i)}
          loadOptions={keyword => allReactSelectSearches(keyword, [sourceTheme.id])}
          styles={{ option: base => ({ ...base, color: '#000' }) }}
        />
      </div>
    ))}
  </>
);
export default ReferencedThemes;
