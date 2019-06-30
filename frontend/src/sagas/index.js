// @flow
import { all, takeEvery, takeLatest } from 'redux-saga/effects';
import type { Saga } from 'redux-saga';
import { FETCH_SINGLE_USER, FETCH_USER_TAGS, EDIT_USER } from '../domain/user/actions';
import { UPLOAD_AVATAR } from '../domain/avatar/actions';
import {SIGN_IN, SIGN_OUT} from '../domain/sign/actions';
import {ADD_TAG, FETCH_ALL_TAGS, FETCH_STARRED_TAGS} from '../domain/tags/actions';
import * as user from '../domain/user/endpoints';
import * as avatar from '../domain/avatar/endpoints';
import * as sign from '../domain/sign/endpoints';
import * as tag from '../domain/tags/endpoints';
import * as bulletpoint from '../domain/bulletpoint/endpoints';
import * as contributedBulletpoint from '../domain/contributed_bulletpoint/endpoints';
import * as theme from '../domain/theme/endpoints';
import {CHANGE_THEME, FETCH_SINGLE_THEME, STAR_OR_UNSTAR_THEME, FETCH_ALL_THEMES} from "../domain/theme/actions";
import {
  ADD_THEME_BULLETPOINT, DELETE_SINGLE_THEME_BULLETPOINT,
  EDIT_THEME_BULLETPOINT,
  FETCH_ALL_BULLETPOINTS, RATE_SINGLE_THEME_BULLETPOINT,
  UPDATE_SINGLE_THEME_BULLETPOINT
} from "../domain/bulletpoint/actions";
import {
  ADD_THEME_CONTRIBUTED_BULLETPOINT,
  DELETE_SINGLE_THEME_CONTRIBUTED_BULLETPOINT,
  FETCH_ALL_CONTRIBUTED_BULLETPOINTS
} from "../domain/contributed_bulletpoint/actions";

export default function* (): Saga {
  yield all([
    takeEvery(DELETE_SINGLE_THEME_BULLETPOINT, bulletpoint.deleteSingle),
    takeEvery(DELETE_SINGLE_THEME_CONTRIBUTED_BULLETPOINT, contributedBulletpoint.deleteSingle),
    takeEvery(FETCH_ALL_BULLETPOINTS, bulletpoint.fetchAll),
    takeEvery(FETCH_ALL_CONTRIBUTED_BULLETPOINTS, contributedBulletpoint.fetchAll),
    takeEvery(FETCH_SINGLE_THEME, theme.fetchSingle),
    takeEvery(FETCH_SINGLE_USER, user.fetchSingle),
    takeEvery(FETCH_USER_TAGS, user.fetchTags),
    takeLatest(ADD_TAG, tag.add),
    takeLatest(ADD_THEME_BULLETPOINT, bulletpoint.add),
    takeLatest(ADD_THEME_CONTRIBUTED_BULLETPOINT, contributedBulletpoint.add),
    takeLatest(CHANGE_THEME, theme.change),
    takeLatest(EDIT_THEME_BULLETPOINT, bulletpoint.edit),
    takeLatest(EDIT_USER, user.edit),
    takeLatest(FETCH_ALL_TAGS, tag.fetchAll),
    takeLatest(FETCH_ALL_THEMES, theme.fetchAll),
    takeLatest(FETCH_STARRED_TAGS, tag.fetchStarred),
    takeLatest(RATE_SINGLE_THEME_BULLETPOINT, bulletpoint.rate),
    takeLatest(SIGN_OUT, sign.signOut),
    takeLatest(STAR_OR_UNSTAR_THEME, theme.starOrUnstar),
    takeLatest(UPDATE_SINGLE_THEME_BULLETPOINT, bulletpoint.updateSingle),
    takeLatest(UPLOAD_AVATAR, avatar.upload),
    takeLatest(SIGN_IN, sign.signIn),
  ]);
}
