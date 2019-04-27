// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../../../types';
import * as format from '../../../formats';
import Options from '../Default/Options';
import Details from '../Default/Details';

type Props = {|
  +children: FetchedBulletpointType,
  +onDeleteClick?: () => (void),
|};
const InnerContent = ({
  children,
  onDeleteClick,
}: Props) => (
  <>
    {format.withComparisons(format.replaceMatches(children), children)}
    <Options onDeleteClick={onDeleteClick} />
    <br />
    <Details>{children}</Details>
  </>
);

export default InnerContent;
