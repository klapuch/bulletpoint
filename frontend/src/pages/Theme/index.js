// @flow
import React from 'react';
import styled from 'styled-components';
import { connect } from 'react-redux';
import { single } from '../../theme/endpoints';
import { all, add } from '../../theme/bulletpoint/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';
import {
  allFetching as allThemeBulletpointsFetching,
  getByTheme as getBulletpointsByTheme,
} from '../../theme/bulletpoint/selects';
import Loader from '../../ui/Loader';
import Add from '../../bulletpoint/Add';

type TagProps = {|
  children: string,
|};
const Tag = ({ children }: TagProps) => <span style={{ marginRight: 7 }} className="label label-default">{children}</span>;
type TagsProps = {|
  texts: Array<string>,
|};
const Tags = ({ texts }: TagsProps) => texts.map(text => <Tag key={text}>{text}</Tag>);

type ReferenceProps = {|
  name: string,
  url: string,
|};
const Reference = ({ name, url }: ReferenceProps) => {
  if (name === 'wikipedia') {
    return (
      <a href={url} title="Wikipedia">
        <span className="glyphicon glyphicon-link" aria-hidden="true" />
      </a>
    );
  }
  return null;
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
|};
const Title = styled.h1`
  display: inline-block;
`;
class Theme extends React.Component<Props> {
  componentDidMount(): void {
    this.reload();
  }

  onSubmit(bulletpoint: Object): void {
    const { match: { params: { id } } } = this.props;
    this.props.addThemeBulletpoint(id, bulletpoint, () => this.reload());
  }

  reload(): void {
    const { match: { params: { id } } } = this.props;
    this.props.singleTheme(id);
    this.props.bulletpointsByTheme(id);
  }

  render() {
    const { theme, fetching, bulletpoints } = this.props;
    if (fetching) {
      return <Loader />;
    }
    return (
      <>
        <div>
          <Title>{theme.name}</Title>
          <Reference name={theme.reference.name} url={theme.reference.url} />
        </div>
        <Tags texts={theme.tags} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpointy">Bulletpointy</h2>
            <ul className="list-group">
              {bulletpoints.map((bulletpoint) => {
                return (
                  <li key={`bulletpoint-${bulletpoint.id}`} className="list-group-item">
                    <span className="badge alert-danger badge-guest">
                      {bulletpoint.rating.down}
                      <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
                    </span>
                    <span className="badge alert-success badge-guest">
                      {bulletpoint.rating.up}
                      <span className="glyphicon glyphicon-thumbs-up" aria-hidden="true" />
                    </span>
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
            <Add onSubmit={bulletpoint => this.onSubmit(bulletpoint)} />
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
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
