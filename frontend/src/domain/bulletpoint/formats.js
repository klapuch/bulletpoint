import reactStringReplace from 'react-string-replace';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import React from 'react';
import type { FetchedBulletpointType } from './types';

const REGEX = /\[\[(.+?)\]\]/g;

export const replaceBulletpointMatches = (bulletpoint: FetchedBulletpointType) => {
  const referencedTheme = bulletpoint.referenced_theme;
  let order = 0;
  return reactStringReplace(bulletpoint.content, REGEX, (match) => {
    const currentTheme = referencedTheme[order];
    order += 1;
    return (
      <Link key={order} to={`/themes/${currentTheme.id}/${getSlug(currentTheme.name)}`}>
        {match}
      </Link>
    );
  });
};
