// @flow
import React from 'react';
import { connect } from 'react-redux';
import Star from './Star';
import * as theme from '../domain/theme/endpoints';
import * as themes from '../domain/theme/selects';
import type { FetchedThemeType } from '../domain/theme/types';

type Props = {|
  +starOrUnstar: (boolean) => (Promise<any>),
  +themeId: number,
  +theme: FetchedThemeType,
  +fetchTheme: () => (void),
  +fetching: boolean,
|};
class HttpStar extends React.Component<Props> {
  componentDidMount(): void {
    this.props.fetchTheme();
  }

  componentDidUpdate(prevProps: Props): void {
    if (prevProps.themeId !== this.props.themeId) {
      this.props.fetchTheme();
    }
  }

  render() {
    const { fetching, theme } = this.props;
    if (fetching) {
      return null;
    }
    return (
      <Star
        active={theme.is_starred}
        onClick={this.props.starOrUnstar}
      />
    );
  }
}

const mapStateToProps = (state, { themeId }) => ({
  theme: themes.getById(themeId, state),
  fetching: themes.singleFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { themeId }) => ({
  starOrUnstar: (
    isStarred: boolean,
  ) => theme.starOrUnstar(themeId, isStarred, () => dispatch(theme.updateSingle(themeId))),
  fetchTheme: () => dispatch(theme.fetchSingle(themeId)),
});
export default connect(mapStateToProps, mapDispatchToProps)(HttpStar);
