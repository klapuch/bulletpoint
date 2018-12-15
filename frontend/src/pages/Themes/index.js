// @flow
import React from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';
import qs from 'qs';
import { first, mapValues } from 'lodash';
import { all } from '../../theme/endpoints';
import { getAll, allFetching as themesFetching } from '../../theme/selects';
import Loader from '../../ui/Loader';
import Tags from '../../theme/components/Tags';
import type { FetchedThemeType } from '../../theme/endpoints';

type Props = {|
  +params: {|
    +tag: ?number,
  |},
  +themes: Array<FetchedThemeType>,
  +fetching: boolean,
  +fetchThemes: (tag: ?number) => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    const { tag } = this.props.params;
    this.props.fetchThemes(tag);
  }

  getRelatedTag = (themes: Array<FetchedThemeType>, tagId: ?number) => (
    first(first(themes.map(theme => theme.tags)).filter(tag => tag.id === tagId))
  );

  getTitle = () => {
    const { themes, params: { tag: tagId } } = this.props;
    if (tagId === null) {
      return 'Nedávno přidaná témata';
    }
    const relatedTag = this.getRelatedTag(themes, tagId) || { name: null };
    return <>Témata vybraná pro <strong>{relatedTag.name}</strong></>;
  };

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <h1>{this.getTitle()}</h1>
        <br />
        {themes.map(theme => (
          <React.Fragment key={theme.id}>
            <Link className="no-link" to={`themes/${theme.id}`}>
              <h2>{theme.name}</h2>
            </Link>
            <Tags tags={theme.tags} />
            <hr />
          </React.Fragment>
        ))}
      </>
    );
  }
}

const mapStateToProps = (state, { location: { search } }) => ({
  params: mapValues(
    { tag: null, ...qs.parse(search, { ignoreQueryPrefix: true }) },
    (value, key) => (['tag'].includes(key) && value !== null ? parseInt(value, 10) : value),
  ),
  themes: getAll(state),
  fetching: themesFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchThemes: (tag: ?number) => dispatch(all(tag)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
