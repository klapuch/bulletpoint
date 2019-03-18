// @flow
import React from 'react';
import { isEmpty } from 'lodash';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import type { FetchedThemeType } from '../../../domain/theme/types';

type Props = {|
  +themeId: number,
  +relatedThemes: Array<FetchedThemeType>,
|};
const RelatedThemes = ({ themeId, relatedThemes }: Props) => (
  !isEmpty(relatedThemes) && (
    <>
      <h2 id="related_themes">Související témata</h2>
      <div className="well">
        {relatedThemes.map((relatedTheme, order) => (
          <React.Fragment key={order}>
            {order === 0 ? '' : ', '}
            <Link
              key={order}
              to={{
                state: { highlightedBulletpointIds: [themeId] },
                pathname: `/themes/${relatedTheme.id}/${getSlug(relatedTheme.name)}`,
              }}
            >
              {relatedTheme.name}
            </Link>
          </React.Fragment>
        ))}
      </div>
    </>
  )
);

export default RelatedThemes;
