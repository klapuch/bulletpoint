// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import qs from 'qs';
import { isEmpty } from 'lodash';
import * as theme from '../../../domain/theme/endpoints';
import * as themes from '../../../domain/theme/selects';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';

type Props = {|
  +params: {|
    +q: string,
  |},
  +themes: Array<FetchedThemeType>,
  +fetching: boolean,
  +fetchThemes: (keyword: string) => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    const { params: { q } } = this.props;
    this.props.fetchThemes(q);
  }

  getHeader = () => {
    const { params: { q } } = this.props;
    return <>Výsledky hledání pro &quot;<strong>{q}</strong>&quot;</>;
  };

  getTitle = () => {
    const { params: { q } } = this.props;
    return `Výsledky hledání pro "${q}"`;
  };

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <Helmet>
          <title>{this.getTitle()}</title>
        </Helmet>
        <h1>{this.getHeader()}</h1>
        {themes.length !== 0 && (
        <h3>
          <small>
            {`Počet výsledků: ${themes.length}`}
          </small>
        </h3>
        )}
        <br />
        {isEmpty(themes) ? <h2>Žádné shody</h2> : <Previews themes={themes} />}
      </>
    );
  }
}

const mapStateToProps = (state, { location: { search } }) => ({
  params: { q: null, ...qs.parse(search, { ignoreQueryPrefix: true }) },
  themes: themes.getAll(state),
  fetching: themes.allFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchThemes: (keyword: string) => dispatch(theme.allSearched(keyword)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
