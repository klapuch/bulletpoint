// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';
import { all } from '../../theme/endpoints';
import { getAll, allFetching as themesFetching } from '../../theme/selects';
import Loader from '../../ui/Loader';
import Tags from '../../theme/components/Tags';
import type { FetchedThemeType } from '../../theme/endpoints';

type Props = {|
  +themes: Array<FetchedThemeType>,
  +fetching: boolean,
  +recentThemes: () => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    this.props.recentThemes();
  }

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <h1>Nedávno přidaná témata</h1>
        <br />
        {themes.map(theme => (
          <React.Fragment key={theme.id}>
            <Link className="no-link" to={`themes/${theme.id}`}>
              <h2>{theme.name}</h2>
            </Link>
            <Tags texts={theme.tags} />
            <hr />
          </React.Fragment>
        ))}
      </>
    );
  }
}

const mapStateToProps = state => ({
  themes: getAll(state),
  fetching: themesFetching(state),
});
const mapDispatchToProps = dispatch => ({
  recentThemes: () => dispatch(all()),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
