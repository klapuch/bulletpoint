// @flow
import React from 'react';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { single } from '../../theme/endpoints';
import { all, add } from '../../theme/bulletpoint/endpoints';
import { rate } from '../../theme/bulletpoint/rating/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';
import {
  allFetching as fetchingAllThemeBulletpoints,
  getByTheme as getThemeBulletpoints,
} from '../../theme/bulletpoint/selects';
import Loader from '../../ui/Loader';
import Add from '../../bulletpoint/Add';
import Tags from '../../theme/components/Tags';
import Reference from '../../theme/components/Reference';
import Source from '../../theme/components/Source';
import { UpButton, DownButton } from '../../theme/bulletpoint/components/RateButton';
import type { FetchedThemeType } from '../../theme/endpoints';

const Title = styled.h1`
  display: inline-block;
`;

type Props = {|
  +getTheme: (number) => (void),
  +getBulletpoints: (number) => (void),
  +match: Object,
  +theme: FetchedThemeType,
  +bulletpoints: Array<Object>,
  +fetching: boolean,
  +addBulletpoint: (number, Object, (void) => (void)) => (void),
  +changeRating: (theme: number, bulletpoint: number, point: number, (void) => (void)) => (void),
|};
class Theme extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  onSubmit = (bulletpoint: Object) => {
    const { match: { params: { id } } } = this.props;
    this.props.addBulletpoint(id, bulletpoint, this.reload);
  };

  reload = () => {
    const { match: { params: { id } } } = this.props;
    this.props.getTheme(id);
    this.props.getBulletpoints(id);
  };

  changeRating = (bulletpoint: number, point: number) => {
    const { match: { params: { id } } } = this.props;
    this.props.changeRating(id, bulletpoint, point, this.reload);
  };

  render() {
    const { theme, fetching, bulletpoints } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <div>
          <Title>{theme.name}</Title>
          <Reference url={theme.reference.url} />
        </div>
        <Tags texts={theme.tags} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpointy">Bulletpointy</h2>
            <ul className="list-group">
              {bulletpoints.map(bulletpoint => (
                <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
                  <DownButton onClick={() => this.changeRating(bulletpoint.id, -1)}>
                    {bulletpoint.rating.down}
                  </DownButton>
                  <UpButton onClick={() => this.changeRating(bulletpoint.id, +1)}>
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
            <Add onSubmit={this.onSubmit} />
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
});
const mapDispatchToProps = dispatch => ({
  getTheme: (theme: number) => dispatch(single(theme)),
  addBulletpoint: (
    theme: number,
    bulletpoint: Object,
    next: (void) => (void),
  ) => dispatch(add(theme, bulletpoint, next)),
  getBulletpoints: (theme: number) => dispatch(all(theme)),
  changeRating: (
    theme: number,
    bulletpoint: number,
    point: number,
    next: (void) => (void),
  ) => dispatch(rate(theme, bulletpoint, point, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
