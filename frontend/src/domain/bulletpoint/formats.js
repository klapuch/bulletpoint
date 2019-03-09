import reactStringReplace from 'react-string-replace';
import { Link } from 'react-router-dom';
import getSlug from 'speakingurl';
import React from 'react';
import type { FetchedBulletpointType } from './types';

const REGEX = /\[\[(.+?)\]\]/g;

export const numberOfReferences = (text: string) => (text.match(REGEX) || []).length;

export const replaceMatches = (bulletpoint: FetchedBulletpointType) => {
  const { referenced_theme: referencedTheme } = bulletpoint;
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

export const withComparisons = (content: string, bulletpoint: FetchedBulletpointType) => {
  const { compared_theme: comparedTheme } = bulletpoint;
  if (comparedTheme.length === 0) {
    return content;
  }
  return (
    <em>
      {content}
      {' '}
&hellip; než&nbsp;
      {comparedTheme.map((theme, order) => (
        <React.Fragment key={order}>
          {order === 0 ? '' : ', '}
          <Link to={`/themes/${theme.id}/${getSlug(theme.name)}`}>
            {theme.name}
          </Link>
        </React.Fragment>
      ))}
    </em>
  );
};
