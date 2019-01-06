// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import { isEmpty } from 'lodash';
import * as theme from '../../domain/theme/endpoints';
import * as themes from '../../domain/theme/selects';
import Loader from '../../ui/Loader';
import SlugRedirect from '../../router/SlugRedirect';
import type { FetchedThemeType } from '../../domain/theme/types';
import { default as AllThemes } from '../../domain/theme/components/All';

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
  +fetchRecentThemes: () => (void),
  +fetchTaggedThemes: (tag: number) => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      this.props.fetchRecentThemes();
    } else {
      this.props.fetchTaggedThemes(parseInt(tag, 10));
    }
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { tag } } } = this.props;
    if (prevProps.match.params.tag !== tag) {
      if (isEmpty(tag)) {
        this.props.fetchRecentThemes();
      } else {
        this.props.fetchTaggedThemes(parseInt(tag, 10));
      }
    }
  }

  getHeader = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    return <>Témata vybraná pro "<strong>{this.getTag()}</strong>"</>;
  };

  getTitle = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    return `Témata vybraná pro "${this.getTag()}"`;
  };

  getTag = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return '';
    }
    return themes.getCommonTag(this.props.themes, parseInt(tag, 10));
  };

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.getTag()}>
        <Helmet>
          <title>{this.getTitle()}</title>
        </Helmet>
        <h1>{this.getHeader()}</h1>
        <br />
        <AllThemes themes={themes} />
      </SlugRedirect>
    );
  }
}

const mapStateToProps = state => ({
  themes: themes.getAll(state),
  fetching: themes.allFetching(state),
});
const mapDispatchToProps = dispatch => ({
  fetchRecentThemes: () => dispatch(theme.allRecent()),
  fetchTaggedThemes: (tag: number) => dispatch(theme.allByTag(tag)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
