// @flow
import React from 'react';
import Helmet from 'react-helmet';
import { isEmpty } from 'lodash';
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

type State = {|
  ratings: Object,
|};
type Props = {|
  +fetchTheme: (number) => (void),
  +fetchBulletpoints: (number) => (void),
  +match: Object,
  +theme: FetchedThemeType,
  +bulletpoints: Array<FetchedBulletpointType>,
  +fetching: boolean,
  +addBulletpoint: (number, PostedBulletpointType, (void) => (void)) => (void),
  +changeRating: (theme: number, bulletpoint: number, point: number, (void) => (void)) => (void),
  +getBulletpointById: (number) => FetchedBulletpointType,
|};
class Theme extends React.Component<Props, State> {
  state = {
    ratings: {},
  };

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
    this.setState({ ratings: {} });
  };

  handleRatingChange = (bulletpoint: number, point: number) => {
    const { match: { params: { id } } } = this.props;
    this.props.changeRating(
      id,
      bulletpoint,
      point,
      () => this.setState((prevState) => {
        if (isEmpty(prevState.ratings[bulletpoint])) {
          if (this.props.getBulletpointById(bulletpoint).rating.user === point) {
            return prevState;
          }
          return ({
            ratings: {
              ...prevState.ratings,
              [bulletpoint]: {
                up: point === 1 ? 1 : -1,
                down: point === 1 ? -1 : 1,
              },
            },
          });
        } else if (prevState.ratings[bulletpoint].up === 1 && point === 1) {
          return prevState;
        } else if (prevState.ratings[bulletpoint].down === 1 && point === -1) {
          return prevState;
        }
        return ({ ratings: { ...prevState.ratings, [bulletpoint]: undefined } });
      }),
    );
  };

  render() {
    const { theme, fetching, bulletpoints } = this.props;
    const { ratings } = this.state;
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
              {bulletpoints.map((bulletpoint) => {
                const { up, down } = ratings[bulletpoint.id] || { up: 0, down: 0 };
                const isUp = up === 0 && down === 0 ? bulletpoint.rating.user === 1 : up === 1;
                const isDown = up === 0 && down === 0 ? bulletpoint.rating.user === -1 : down === 1;
                return (
                  <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
                    <DownButton
                      rated={isDown}
                      onClick={() => this.handleRatingChange(bulletpoint.id, -1)}
                    >
                      {bulletpoint.rating.down + down}
                    </DownButton>
                    <UpButton
                      rated={isUp}
                      onClick={() => this.handleRatingChange(bulletpoint.id, +1)}
                    >
                      {bulletpoint.rating.up + up}
                    </UpButton>
                    {bulletpoint.content}
                    <br />
                    <small>
                      <cite>
                        <Source type={bulletpoint.source.type} link={bulletpoint.source.link} />
                      </cite>
                    </small>
                  </li>
                );
              })}
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
    next: (void) => (void),
  ) => dispatch(rate(theme, bulletpoint, point, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
