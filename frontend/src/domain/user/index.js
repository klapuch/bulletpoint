// @flow
import * as session from '../access/session';
import type { FetchedUserType, MeType, RoleType } from './types';

export const isLoggedIn = (): bool => session.exists();

export function getRole(): ?RoleType {
  return isLoggedIn() ? session.getMe().role : null;
}

export function getUsername(): string|null {
  return isLoggedIn() ? session.getMe().username : null;
}

export function getMe(): MeType|null {
  return isLoggedIn() ? session.getMe() : null;
}

export function getAvatar(user: FetchedUserType, width: number, height: number): string {
  return `${process.env.REACT_APP_STATIC || ''}/${user.avatar_filename}?w=${width}&h=${height}`;
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
