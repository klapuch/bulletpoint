// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import qs from 'qs';
import { isEmpty } from 'lodash';
import * as theme from '../../../domain/theme/actions';
import * as themes from '../../../domain/theme/selects';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Previews from '../../../domain/theme/components/Previews';
import SkeletonPreviews from '../../../domain/theme/components/SkeletonPreviews';

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
    this.props.fetchThemes(this.props.params.q);
  }

  getHeader = () => <>Výsledky hledání pro &quot;<strong>{this.props.params.q}</strong>&quot;</>;

  getTitle = () => `Výsledky hledání pro "${this.props.params.q}"`;

  render() {
    const { themes, fetching } = this.props;
    return (
      <>
        <Helmet>
          <title>{this.getTitle()}</title>
        </Helmet>
        <h1>{this.getHeader()}</h1>
        {!fetching && themes.length !== 0 && (
          <h3>
            <small>
              {`Počet výsledků: ${themes.length}`}
            </small>
          </h3>
        )}
        <br />
        {!fetching && isEmpty(themes)
          ? <h2>Žádné shody</h2>
          : (
            fetching
              ? <SkeletonPreviews>{1}</SkeletonPreviews>
              : <Previews tagLink={(id, slug) => `/themes/tag/${id}/${slug}`} themes={themes} />
          )}
      </>
    );
  }
}

const mapStateToProps = (state, { location: { search } }) => ({
  params: { q: null, ...qs.parse(search, { ignoreQueryPrefix: true }) },
  themes: themes.getAll(state),
  fetching: themes.isAllFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchThemes: (keyword: string) => dispatch(theme.fetchSearches(keyword)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
