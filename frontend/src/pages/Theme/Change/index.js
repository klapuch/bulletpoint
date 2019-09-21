// @flow
import React from 'react';
import { connect } from 'react-redux';
import Helmet from 'react-helmet';
import * as theme from '../../../domain/theme/actions';
import * as themes from '../../../domain/theme/selects';
import Loader from '../../../ui/Loader';
import type { FetchedThemeType } from '../../../domain/theme/types';
import Form from '../../../domain/theme/components/Form/ChangeHttpForm';

type Props = {|
  +fetchSingle: () => (void),
  +fetching: boolean,
  +history: Object,
  +match: Object,
  +theme: FetchedThemeType,
|};
class Create extends React.Component<Props> {
  componentDidMount(): void {
    this.props.fetchSingle();
  }

  getTitle = (name: string) => `Úprava tématu "${name}"`;

  render() {
    const { fetching, theme } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <Helmet>
          <title>{this.getTitle(theme.name)}</title>
        </Helmet>
        <h1>{this.getTitle(theme.name)}</h1>
        <Form
          history={this.props.history}
          match={this.props.match}
          theme={this.props.theme}
        />
      </>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id } } }) => ({
  theme: themes.getById(id, state),
  fetching: themes.isFetching(id, state),
});
const mapDispatchToProps = (dispatch, { match: { params: { id } } }) => ({
  fetchSingle: () => dispatch(theme.fetchSingle(id)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Create);
