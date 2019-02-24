export type FormTypes = 'default' | 'edit' | 'add';
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
