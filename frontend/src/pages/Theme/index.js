// @flow
import React from 'react';
import Helmet from 'react-helmet';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { single } from '../../theme/endpoints';
import { all, add } from '../../theme/bulletpoint/endpoints';
import { rate } from '../../theme/bulletpoint/rating/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';
import * as session from '../../access/session';
import {
  allFetching as fetchingAllThemeBulletpoints,
  getByTheme as getThemeBulletpoints,
  getById as getBulletpointById,
} from '../../theme/bulletpoint/selects';
import Loader from '../../ui/Loader';
import Add from '../../bulletpoint/Add';
import Tags from '../../theme/components/Tags';
import Reference from '../../theme/components/Reference';
import Source from '../../theme/components/Source';
import { UpButton, DownButton } from '../../theme/bulletpoint/components/RateButton';
import type { FetchedThemeType } from '../../theme/types';
import type { FetchedBulletpointType, PostedBulletpointType } from '../../theme/bulletpoint/types';

const Title = styled.h1`
  display: inline-block;
`;

type Props = {|
  +fetchTheme: (number) => (void),
  +fetchBulletpoints: (number) => (void),
  +match: Object,
  +theme: FetchedThemeType,
  +bulletpoints: Array<FetchedBulletpointType>,
  +fetching: boolean,
  +addBulletpoint: (number, PostedBulletpointType, (void) => (void)) => (void),
  +changeRating: (theme: number, bulletpoint: number, point: number) => (void),
  +getBulletpointById: (number) => FetchedBulletpointType,
|};
class Theme extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  handleSubmit = (bulletpoint: PostedBulletpointType) => {
    const { match: { params: { id } } } = this.props;
    this.props.addBulletpoint(id, bulletpoint, this.reload);
  };

  reload = () => {
    const { match: { params: { id } } } = this.props;
    this.props.fetchTheme(id);
    this.props.fetchBulletpoints(id);
  };

  handleRatingChange = (bulletpointId: number, point: number) => {
    const { match: { params: { id } } } = this.props;
    const bulletpoint = this.props.getBulletpointById(bulletpointId);
    this.props.changeRating(id, bulletpointId, bulletpoint.rating.user === point ? 0 : point);
  };

  render() {
    const { theme, fetching, bulletpoints } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <Helmet><title>{theme.name}</title></Helmet>
        <div>
          <Title>{theme.name}</Title>
          <Reference url={theme.reference.url} />
        </div>
        <Tags tags={theme.tags} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpointy">Bulletpointy</h2>
            <ul className="list-group">
              {bulletpoints.map(bulletpoint => (
                <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
                  <DownButton
                    rated={bulletpoint.rating.user === -1}
                    onClick={() => this.handleRatingChange(bulletpoint.id, -1)}
                  >
                    {bulletpoint.rating.down}
                  </DownButton>
                  <UpButton
                    rated={bulletpoint.rating.user === 1}
                    onClick={() => this.handleRatingChange(bulletpoint.id, +1)}
                  >
                    {bulletpoint.rating.up}
                  </UpButton>
                  {bulletpoint.content}
                  <br />
                  <small>
                    <cite>
                      <Source type={bulletpoint.source.type} link={bulletpoint.source.link} />
                    </cite>
                  </small>
                </li>
              ))}
            </ul>
            {session.exists() ? <Add onSubmit={this.handleSubmit} /> : null}
          </div>
        </div>
        <br />
      </>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id: theme } } }) => ({
  theme: getById(theme, state),
  bulletpoints: getThemeBulletpoints(theme, state),
  fetching: themeFetching(theme, state) || fetchingAllThemeBulletpoints(theme, state),
  getBulletpointById: (bulletpoint: number) => getBulletpointById(theme, bulletpoint, state),
});
const mapDispatchToProps = dispatch => ({
  fetchTheme: (theme: number) => dispatch(single(theme)),
  addBulletpoint: (
    theme: number,
    bulletpoint: PostedBulletpointType,
    next: (void) => (void),
  ) => dispatch(add(theme, bulletpoint, next)),
  fetchBulletpoints: (theme: number) => dispatch(all(theme)),
  changeRating: (
    theme: number,
    bulletpoint: number,
    point: number,
  ) => dispatch(rate(theme, bulletpoint, point)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
