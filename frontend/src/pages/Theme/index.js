// @flow
import React from 'react';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { single } from '../../theme/endpoints';
import { all, add } from '../../theme/bulletpoint/endpoints';
import { rate } from '../../theme/bulletpoint/rating/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';
import {
  allFetching as allThemeBulletpointsFetching,
  getByTheme as getBulletpointsByTheme,
} from '../../theme/bulletpoint/selects';
import Loader from '../../ui/Loader';
import Add from '../../bulletpoint/Add';


const RateButton = styled.span`
  cursor: pointer;
`;

type TagProps = {|
  children: string,
|};
const Tag = ({ children }: TagProps) => <span style={{ marginRight: 7 }} className="label label-default">{children}</span>;
type TagsProps = {|
  texts: Array<string>,
|};
const Tags = ({ texts }: TagsProps) => texts.map(text => <Tag key={text}>{text}</Tag>);

type ReferenceProps = {|
  url: string,
|};
const Reference = ({ url }: ReferenceProps) => {
  return (
    <a href={url} title="Wikipedia">
      <span className="glyphicon glyphicon-link" aria-hidden="true" />
    </a>
  );
};
type SourceProps = {|
  type: string,
  link: string,
|};
const Source = ({ type, link }: SourceProps) => {
  if (type === 'web') {
    return (
      <>
        <span style={{ marginRight: 4 }} className="glyphicon glyphicon-globe" aria-hidden="true" />
        <a href={link}>{link}</a>
      </>
    );
  }
  return null;
};

type Props = {|
  +singleTheme: (number) => (void),
  +bulletpointsByTheme: (number) => (void),
  +match: Object,
  +theme: Object,
  +bulletpoints: Array<Object>,
  +fetching: boolean,
  +addThemeBulletpoint: (number, Object, (void) => (void)) => (void),
  +changeRating: (number, number, number, (void) => (void)) => (void),
|};
const Title = styled.h1`
  display: inline-block;
`;
class Theme extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  onSubmit = (bulletpoint: Object) => {
    const { match: { params: { id } } } = this.props;
    this.props.addThemeBulletpoint(id, bulletpoint, this.reload);
  };

  reload = () => {
    const { match: { params: { id } } } = this.props;
    this.props.singleTheme(id);
    this.props.bulletpointsByTheme(id);
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
              {bulletpoints.map((bulletpoint) => {
                return (
                  <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
                    <RateButton className="badge alert-danger badge-guest" onClick={() => this.changeRating(bulletpoint.id, -1)}>
                      {bulletpoint.rating.down}
                      <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
                    </RateButton>
                    <RateButton className="badge alert-success badge-guest" onClick={() => this.changeRating(bulletpoint.id, +1)}>
                      {bulletpoint.rating.up}
                      <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
                    </RateButton>
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
  bulletpoints: getBulletpointsByTheme(theme, state),
  fetching: themeFetching(theme, state) || allThemeBulletpointsFetching(theme, state),
});
const mapDispatchToProps = dispatch => ({
  singleTheme: (theme: number) => dispatch(single(theme)),
  addThemeBulletpoint: (
    theme: number,
    bulletpoint: Object,
    next: (void) => (void),
  ) => dispatch(add(theme, bulletpoint, next)),
  bulletpointsByTheme: (theme: number) => dispatch(all(theme)),
  changeRating: (
    theme: number,
    bulletpoint: number,
    point: number,
    next: (void) => (void),
  ) => dispatch(rate(theme, bulletpoint, point, next)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
