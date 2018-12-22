// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import { Link } from 'react-router-dom';
import { isEmpty } from 'lodash';
import { allByTag, allRecent } from '../../theme/endpoints';
import { getAll, allFetching as themesFetching, getCommonTag } from '../../theme/selects';
import Loader from '../../ui/Loader';
import Tags from '../../theme/components/Tags';
import type { FetchedThemeType } from '../../theme/types';

type Props = {|
  +params: {|
    +tag: ?number,
  |},
  +match: {|
    +params: {|
      +tag: ?string,
    |},
  |},
  +themes: Array<FetchedThemeType>,
  +fetching: boolean,
  +fetchThemes: (tag: ?number) => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    const { match: { params: { tag } } } = this.props;
    this.props.fetchThemes(isEmpty(tag) ? null : parseInt(tag, 10));
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { tag } } } = this.props;
    if (prevProps.match.params.tag !== tag) {
      this.props.fetchThemes(parseInt(tag, 10));
    }
  }

  getHeader = () => {
    const { themes, match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    const commonTag = getCommonTag(themes, parseInt(tag, 10)) || { name: '' };
    return <>Témata vybraná pro "<strong>{commonTag.name}</strong>"</>;
  };

  getTitle = () => {
    const { themes, match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    const commonTag = getCommonTag(themes, parseInt(tag, 10)) || { name: '' };
    return `Témata vybraná pro ${commonTag.name}`;
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

const mapStateToProps = state => ({
  themes: getAll(state),
  fetching: themesFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchThemes: (tag: ?number) => dispatch(tag === null ? allRecent() : allByTag(tag)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
