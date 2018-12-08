// @flow
import React from 'react';

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
	render() {
		return (
			<>
				<h1>PHP</h1>
				<span className="label label-default">Default</span>
				<span className="label label-default">Default</span>
				<span className="label label-default">Default</span>
				<div className="row">
					<div className="col-sm-8">
						<h2 id="bulletpointy">Bulletpoints</h2>
						<ul className="list-group">
							<li id="bulletpoint-{$bulletpoint->id()}" className="list-group-item">
								<a className="rating-badge ajax no-link" href="proti!"><span class="badge alert-danger opposite-rating">4<span className="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></span></a>
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
						<a className="btn btn-default" href="Bulletpoint:pridat $slug" role="button">Add bulletpoint</a>
					</div>
				</div>
				<br/>
			</>
		);
	}
}

export default Theme;
