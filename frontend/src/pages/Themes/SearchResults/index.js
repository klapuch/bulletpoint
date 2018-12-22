// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import qs from 'qs';
import { isEmpty } from 'lodash';
import { allSearched } from '../../../theme/endpoints';
import { getAll, allFetching as themesFetching } from '../../../theme/selects';
import Loader from '../../../ui/Loader';
import { default as AllThemes } from '../../../theme/All';
import type { FetchedThemeType } from '../../../theme/types';

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
    return <>Výsledky hledání pro "<strong>{q}</strong>"</>;
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
        <br />
        {isEmpty(themes) ? <h2>Žádné shody</h2> : <AllThemes themes={themes} />}
      </>
    );
  }
}

const mapStateToProps = (state, { location: { search } }) => ({
  params: { q: null, ...qs.parse(search, { ignoreQueryPrefix: true }) },
  themes: getAll(state),
  fetching: themesFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchThemes: (keyword: string) => dispatch(allSearched(keyword)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
