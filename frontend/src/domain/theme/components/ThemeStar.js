// @flow
import React from 'react';
import { connect } from 'react-redux';
import Star from '../../../components/Star';
import * as theme from '../actions';
import * as themes from '../selects';

type Props = {|
  +starOrUnstar: (boolean) => (void),
  +isStarred: boolean,
|};
class ThemeStar extends React.PureComponent<Props> {
  render() {
    const { isStarred, starOrUnstar } = this.props;
    return (
      <Star
        active={isStarred}
        onClick={starOrUnstar}
      />
    );
  }
}

const mapStateToProps = (state, { theme: { id } }) => ({
  isStarred: themes.isStarred(id, state),
});
const mapDispatchToProps = (dispatch, { theme: { id } }) => ({
  starOrUnstar: (starred: boolean) => dispatch(theme.starOrUnstar(id, starred)),
});
export default connect(mapStateToProps, mapDispatchToProps)(ThemeStar);
