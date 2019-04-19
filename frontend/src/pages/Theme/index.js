// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { connect } from 'react-redux';
import * as theme from '../../domain/theme/endpoints';
import * as themes from '../../domain/theme/selects';
import * as user from '../../domain/user';
import DetailBoxes from '../../domain/bulletpoint/components/DetailBoxes';
import Header from '../../domain/theme/components/Header';
import HttpEditForms from '../../domain/bulletpoint/components/Form/HttpEditForms';
import HttpAddForm from '../../domain/bulletpoint/components/Form/HttpAddForm';
import Loader from '../../ui/Loader';
import RelatedThemes from './sections/RelatedThemes';
import SlugRedirect from '../../router/SlugRedirect';
import ContributionBoxes from '../../domain/bulletpoint/components/ContributionBoxes';
import type { FetchedThemeType } from '../../domain/theme/types';
import type { FormTypes } from '../../domain/bulletpoint/components/Form/types';
import { FORM_TYPE_ADD, FORM_TYPE_DEFAULT, FORM_TYPE_EDIT } from '../../domain/bulletpoint/components/Form/types';
import AddButton from '../../domain/bulletpoint/components/Form/AddButton';

type State = {|
  formType: FormTypes,
  bulletpointId: number|null,
|};
type Props = {|
  +history: Object,
  +fetchTheme: () => (void),
  +fetching: boolean,
  +match: Object,
  +theme: FetchedThemeType,
|};
const initState = {
  formType: FORM_TYPE_DEFAULT,
  bulletpointId: null,
};
class Theme extends React.Component<Props, State> {
  state = initState;

  componentDidMount(): void {
    this.reload();
  }

  componentDidUpdate(prevProps: Props) {
    const { match: { params: { id } } } = this.props;
    if (prevProps.match.params.id !== id) {
      this.reload();
    }
  }

  handleAddClick = () => this.setState({ formType: FORM_TYPE_ADD });

  handleEditClick = (id: number) => {
    this.setState({ formType: FORM_TYPE_EDIT, bulletpointId: id });
  };

  handleCancelClick = () => this.setState(initState);

  handleFormTypeChange = (formType: FormTypes) => this.setState({ formType });

  reload = () => {
    this.setState(initState);
    this.props.fetchTheme();
  };

  render() {
    const { formType } = this.state;
    const { fetching, match: { params: { id } }, theme } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <SlugRedirect {...this.props} name={theme.name}>
        <Helmet><title>{theme.name}</title></Helmet>
        <Header theme={theme} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpoints">Bulletpointy</h2>
            <DetailBoxes
              history={this.props.history}
              themeId={id}
              onEditClick={this.handleEditClick}
            />
            {user.isMember() && (<ContributionBoxes themeId={id} />)}
            {user.isLoggedIn() && (
              <HttpEditForms
                themeId={id}
                onCancelClick={this.handleCancelClick}
                bulletpointId={this.state.bulletpointId}
                onFormTypeChange={this.handleFormTypeChange}
                formType={formType}
              />
            )}
            {![FORM_TYPE_ADD, FORM_TYPE_EDIT].includes(formType)
              && <AddButton onClick={this.handleAddClick} />}
            {user.isLoggedIn() && formType === FORM_TYPE_ADD && (
              <HttpAddForm
                themeId={id}
                onCancelClick={this.handleCancelClick}
                onFormTypeChange={this.handleFormTypeChange}
                formType={formType}
              />
            )}
            <RelatedThemes
              themeId={parseInt(id, 10)}
              relatedThemes={theme.related_themes}
            />
          </div>
        </div>
        <br />
      </SlugRedirect>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id: themeId } } }) => ({
  theme: themes.getById(themeId, state),
  fetching: themes.singleFetching(themeId, state),
});
const mapDispatchToProps = (dispatch, { match: { params: { id: themeId } } }) => ({
  fetchTheme: () => dispatch(theme.fetchSingle(themeId)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
