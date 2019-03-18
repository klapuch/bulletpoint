// @flow
import React, { useState } from 'react';
import styled from 'styled-components';
import className from 'classnames';
import { intersection, isEmpty } from 'lodash';
import type { FetchedBulletpointType, PointType } from '../types';
import InnerContent from './InnerContent';

const MoreButton = styled.span`
  float: right;
  margin-right: -14px;
  margin-top: 14px;
  color: #7e7e7e;
  cursor: pointer;
`;

const LessButton = styled.span`
  float: right;
  margin-right: -14px;
  margin-top: 14px;
  color: #ffffff;
  cursor: pointer;
`;

const isHighlighted = (
  id: Array<number>,
  referencedThemeId: Array<number>,
  comparedThemeId: Array<number>,
) => (
  !isEmpty(intersection(id, [...referencedThemeId, ...comparedThemeId]))
);

type Props = {|
  +bulletpoint: FetchedBulletpointType,
  +highlights?: Array<number>,
  +onRatingChange?: (id: number, point: PointType) => (void),
  +onEditClick?: (number) => (void),
  +onDeleteClick?: (number) => (void),
|};
const Box = ({
  bulletpoint,
  highlights = [],
  onRatingChange,
  onEditClick,
  onDeleteClick,
}: Props) => {
  const [more, showMore] = useState(false);
  return (
    <li
      style={{ height: more ? 300 : 'initial' }}
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
      {more
        ? (
          <LessButton
            title="Méně"
            onClick={() => showMore(false)}
            className="glyphicon glyphicon-chevron-up"
            aria-hidden="true"
          />
        )
        : (
          <MoreButton
            title="Více"
            onClick={() => showMore(true)}
            className="glyphicon glyphicon-chevron-down"
            aria-hidden="true"
          />
        )
      }

    </li>
  );
};

export default Box;
