import React from 'react';
import type {FetchedBulletpointType, PostedBulletpointType} from "../../../types";

type Props = {|
  +onSelectChange: (Object) => (void),
  +bulletpoint: PostedBulletpointType,
  +groups: Array<FetchedBulletpointType>,
|};
export default function ({ onSelectChange, bulletpoint, groups }: Props) {
  return (
    <div className="form-group">
      <label htmlFor="group_root_bulletpoint_id">Skupina</label>
      <select
        className="form-control"
        id="group_root_bulletpoint_id"
        name="group_root_bulletpoint_id"
        value={bulletpoint.group.root_bulletpoint_id}
        onChange={onSelectChange}
      >
        <option style={{ fontStyle: 'italic' }} value={0}>Bez skupiny</option>
        {groups.map(group => (
          <option key={group.id} value={group.id}>
            {group.content}
          </option>
        ))}
      </select>
    </div>
  );
};
