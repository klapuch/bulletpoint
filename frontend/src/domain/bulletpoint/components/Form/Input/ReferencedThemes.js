// $FlowFixMe ok
import AsyncSelect from 'react-select/lib/Async';
import {allReactSelectSearches} from "../../../../theme/endpoints";
import type { PreparedReferencedThemesType } from "../types";
import React from "react";

type Props = {|
  +id: number,
  +onSelectChange: (?Object, Object, number) => (void),
  +themes: PreparedReferencedThemesType,
|};
const ReferencedThemes = ({ id, onSelectChange, themes }: Props) => (
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
export default ReferencedThemes;