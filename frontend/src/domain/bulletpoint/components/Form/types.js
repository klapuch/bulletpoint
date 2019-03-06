export const FORM_TYPE_DEFAULT = 'default';
export type FormTypes = FORM_TYPE_DEFAULT | 'edit' | 'add';
export type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};
export type ButtonProps = {|
  formType: FormTypes,
  onClick: () => (void),
|};
export type ReferencedThemesType = Array<{
  id: ?number,
  name: ?string,
}>;
export type ComparedThemesType = Array<{
  id: ?number,
  name: ?string,
}>;
