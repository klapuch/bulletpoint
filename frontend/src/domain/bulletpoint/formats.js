export const references = (value: string) => value.match(/\[\[.+?\]\]/g) || [];
export const withoutMarks = (references: Array<string>) => references.map(match => match.substr(2, match.length - 4));