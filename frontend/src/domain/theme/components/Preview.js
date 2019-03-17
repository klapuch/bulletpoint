// @flow
import React from 'react';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import Labels from '../../tags/components/Labels';
import type { FetchedThemeType } from '../types';

type Props = {|
  +children: FetchedThemeType,
|};
const Preview = ({ children: theme }: Props) => (
  <>
    <Link className="no-link" to={`/themes/${theme.id}/${getSlug(theme.name)}`}>
      <h2>{theme.name}</h2>
    </Link>
    <div>
      <small>
        {theme.alternative_names.join(', ')}
      </small>
    </div>
    <Labels tags={theme.tags} />
  </>
);

export default Preview;