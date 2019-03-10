export const FORM_TYPE_DEFAULT = 'hidden';
export const FORM_TYPE_ADD = 'add';
export const FORM_TYPE_EDIT = 'edit';
export type FormTypes = FORM_TYPE_DEFAULT | FORM_TYPE_EDIT | FORM_TYPE_ADD;
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
