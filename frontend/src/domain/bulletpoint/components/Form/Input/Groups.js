import React from 'react';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../../types';
import { withoutMatches } from '../../../formats';

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
        disabled={hasChildrens || roots.length === 0}
        className="form-control"
        id="group_root_bulletpoint_id"
        name="group_root_bulletpoint_id"
        value={bulletpoint.group.root_bulletpoint_id}
        onChange={onSelectChange}
      >
        <option style={{ fontStyle: 'italic' }} value={0}>Bez skupiny</option>
        {roots.map(group => (
          <option key={group.id} value={group.id}>
            {withoutMatches(group.content)}
          </option>
        ))}
      </select>
    </div>
  );
}
