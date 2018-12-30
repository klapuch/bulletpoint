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
  +fetchThemes: (tag: ?number) => (void),
|};
class Themes extends React.Component<Props> {
  componentDidMount(): void {
    const { match: { params: { tag } } } = this.props;
    this.props.fetchThemes(isEmpty(tag) ? null : parseInt(tag, 10));
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { tag } } } = this.props;
    if (!isEmpty(tag) && prevProps.match.params.tag !== tag) {
      this.props.fetchThemes(parseInt(tag, 10));
    }
  }

  getHeader = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    return <>Témata vybraná pro "<strong>{this.getTag().name}</strong>"</>;
  };

  getTitle = () => {
    const { match: { params: { tag } } } = this.props;
    if (isEmpty(tag)) {
      return 'Nedávno přidaná témata';
    }
    return `Témata vybraná pro "${this.getTag().name}"`;
  };

  getTag = () => {
    const { match: { params: { tag } } } = this.props;
    const initTag = { name: '' };
    if (isEmpty(tag)) {
      return initTag;
    }
    return themes.getCommonTag(this.props.themes, parseInt(tag, 10)) || initTag;
  };

  render() {
    const { themes, fetching } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={this.getTag().name}>
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
  fetchThemes: (tag: ?number) => dispatch(tag === null ? theme.allRecent() : theme.allByTag(tag)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Themes);
