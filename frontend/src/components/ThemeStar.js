// @flow
import React from 'react';
import { connect } from 'react-redux';
import Star from './Star';
import * as theme from '../domain/theme/endpoints';
import type { FetchedThemeType } from '../domain/theme/types';

type Props = {|
  +starOrUnstar: (boolean) => (Promise<any>),
  +theme: FetchedThemeType,
|};
class ThemeStar extends React.PureComponent<Props> {
  render() {
    const { theme } = this.props;
    return (
      <Star
        active={theme.is_starred}
        onClick={this.props.starOrUnstar}
      />
    );
  }
}

const mapDispatchToProps = (dispatch, { theme: { id } }) => ({
  starOrUnstar: (
    isStarred: boolean,
  ) => theme.starOrUnstar(id, isStarred, () => dispatch(theme.updateSingle(id))),
});
export default connect(null, mapDispatchToProps)(ThemeStar);