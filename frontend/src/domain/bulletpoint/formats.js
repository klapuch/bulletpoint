export const REGEX = /\[\[(.+?)\]\]/g;
export const references = (value: string) => value.match(REGEX) || [];
export const withoutMarks = (references: Array<string>) => references.map(match => match.substr(2, match.length - 4));