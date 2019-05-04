import React from 'react';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../../types';

type Props = {|
  +onSelectChange: (Object) => (void),
  +bulletpoint: PostedBulletpointType,
  +roots: Array<FetchedBulletpointType>,
  +hasChildrens: boolean,
|};
export default function ({
  onSelectChange, hasChildrens, bulletpoint, roots,
}: Props) {
  return (
    <div className="form-group">
      <label htmlFor="group_root_bulletpoint_id">Skupina</label>
      <select
        disabled={hasChildrens}
        className="form-control"
        id="group_root_bulletpoint_id"
        name="group_root_bulletpoint_id"
        value={bulletpoint.group.root_bulletpoint_id}
        onChange={onSelectChange}
      >
        <option style={{ fontStyle: 'italic' }} value={0}>Bez skupiny</option>
        {roots.map(group => (
          <option key={group.id} value={group.id}>
            {group.content}
          </option>
        ))}
      </select>
    </div>
  );
}