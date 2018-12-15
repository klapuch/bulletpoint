// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';
import { all } from '../../theme/endpoints';
import { getAll, allFetching as themesFetching } from '../../theme/selects';
import Loader from '../../ui/Loader';

type Props = {|
  +singleTheme: (number) => (void),
  +bulletpointsByTheme: (number) => (void),
  +match: Object,
  +theme: Object,
  +bulletpoints: Array<Object>,
  +fetching: boolean,
  +addThemeBulletpoint: (number, Object, (void) => (void)) => (void),
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
        <div className="table-responsive">
          <table className="table table-hover">
            <thead>
            <tr>
              <th><p>Datum</p></th>
              <th><p>Název</p></th>
            </tr>
            </thead>
            <tbody>
            {themes.map(theme => (
              <tr key={theme.id}>
                <td><p>{theme.created_at}</p></td>
                <td>
                  <p>
                    <Link to={`themes/${theme.id}`}>
                      {theme.name}
                    </Link>
                  </p>
                </td>
              </tr>
            ))}
            </tbody>
          </table>
        </div>
      </>
    );
  }
}

const mapStateToProps = (state) => ({
  themes: getAll(state),
  fetching: themesFetching(state),
});
const mapDispatchToProps = dispatch => ({
  recentThemes: () => dispatch(all()),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
