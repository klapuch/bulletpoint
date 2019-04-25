// @flow
import React from 'react';
import { connect } from 'react-redux';
import Star from '../../../components/Star';
import * as theme from '../endpoints';
import * as themes from '../selects';

type Props = {|
  +starOrUnstar: (boolean) => (Promise<any>),
  +isStarred: boolean,
|};
class ThemeStar extends React.PureComponent<Props> {
  render() {
    const { isStarred } = this.props;
    return (
      <Star
        active={isStarred}
        onClick={this.props.starOrUnstar}
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
