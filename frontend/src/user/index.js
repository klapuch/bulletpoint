// @flow
import * as session from '../access/session';
import type { RoleType } from './types';

export const isLoggedIn = (): bool => session.exists();

export function getRole(): ?RoleType {
  return isLoggedIn() ? session.getMe().role : null;
}

export function getUsername(): ?string {
  return isLoggedIn() ? session.getMe().username : null;
}

export function getEmail(): ?string {
  return isLoggedIn() ? session.getMe().email : null;
}

export function isMember(): bool {
  return session.getMe().role === 'member';
}

export function isAdmin(): bool {
  return session.getMe().role === 'admin';
}
