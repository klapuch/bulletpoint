// @flow
import React from 'react';
import { connect } from 'react-redux';
import { single } from '../../theme/endpoints';
import { getById, singleFetching as themeFetching } from '../../theme/selects';

const Tag = ({ children }) => <span className="label label-default">{children}</span>;
type TagsProps = {|
  texts: Array<string>,
|};
const Tags = ({ texts }: TagsProps) => texts.map(text => <Tag key={text}>{text}</Tag>);

type Props = {|
  +signIn: (Credentials, () => (void)) => (void),
  +location: Object,
|};
type State = {|
  credentials: Credentials,
  errors: CredentialsErrors,
  redirectToReferrer: boolean,
|};
class Theme extends React.Component<Props, State> {
  componentDidMount = () => {
    const { match: { params: { id } } } = this.props;
    this.props.single(id);
  };

  render() {
    console.log(this.props);
    return (
      <>
        <h1>PHP</h1>
        <Tags texts={['A', 'B', 'C']} />
        <div className="row">
          <div className="col-sm-8">
            <h2 id="bulletpointy">Bulletpoints</h2>
            <ul className="list-group">
              <li id="bulletpoint-{$bulletpoint->id()}" className="list-group-item">
                <a className="rating-badge ajax no-link" href="proti!"><span className="badge alert-danger opposite-rating">4<span className="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></span></a>
                <span className="badge alert-success badge-guest">5<span className="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span></span>
                A
                <br/>
                <small>
                  <cite>
                    Zdroj
                  </cite>
                </small>
              </li>
              <li id="bulletpoint-{$bulletpoint->id()}" className="list-group-item">
                <span className="badge alert-danger badge-guest">1<span className="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></span>
                <span className="badge alert-success badge-guest">2<span className="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span></span>
                B
                <br/>
                <small>
                  <cite>
                    Zdroj
                  </cite>
                </small>
              </li>
            </ul>
            <a className="btn btn-default" href="#" role="button">Add bulletpoint</a>
          </div>
        </div>
        <br/>
      </>
    );
  }
}

const mapStateToProps = (state, { match: { params: { id } } }) => ({
  theme: getById(id, state),
  fetching: themeFetching(id, state)
});
const mapDispatchToProps = dispatch => ({
  single: (theme: number) => dispatch(single(theme)),
});
export default connect(mapStateToProps, mapDispatchToProps)(Theme);
