// @flow
import * as session from '../access/session';
import type { RoleType } from './types';

export function getRole(): ?RoleType {
  return session.exists() ? session.getMe().role : null;
}

export function getUsername(): ?string {
  return session.exists() ? session.getMe().username : null;
}

export function getEmail(): ?string {
  return session.exists() ? session.getMe().email : null;
}

export function isMember(): bool {
  return session.getMe().role === 'member';
}

export function isAdmin(): bool {
  return session.getMe().role === 'admin';
}
