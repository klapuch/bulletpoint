// @flow
import React from 'react';
import type { FetchedBulletpointType } from '../types';
import * as format from '../formats';
import Options from './Options';
import Details from './Details';

type Props = {|
  +children: FetchedBulletpointType,
  +onDeleteClick?: () => (void),
|};
const InnerContributionContent = ({
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

export default InnerContributionContent;
