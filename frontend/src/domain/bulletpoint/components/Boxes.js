// @flow
import React from 'react';
import className from 'classnames';
import { intersection, isEmpty } from 'lodash';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';

const isHighlighted = (
  id: Array<number>,
  referencedThemeId: Array<number>,
  comparedThemeId: Array<number>,
) => (
  !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
);

type Props = {|
  +highlights?: Array<number>,
  +bulletpoints: Array<FetchedBulletpointType>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const Boxes = ({
  highlights = [],
  bulletpoints,
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => (
  <ul className="list-group">
    {bulletpoints.map(bulletpoint => (
      <li
        key={`bulletpoint-${bulletpoint.id}`}
        className={className(
          'list-group-item',
          isHighlighted(
            highlights,
            bulletpoint.referenced_theme_id,
            bulletpoint.compared_theme_id,
          ) && 'active',
        )}
      >
        <InnerContent
          onRatingChange={onRatingChange}
          onEditClick={onEditClick}
          onDeleteClick={onDeleteClick}
        >
          {bulletpoint}
        </InnerContent>
      </li>
    ))}
  </ul>
);

export default Boxes;
