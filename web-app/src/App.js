import React from 'react';

import './styles/App.scss';

import {BrowserRouter as Router, Link, Route, Switch} from "react-router-dom";
import PlayersList from "./components/pages/PlayersList";
import PlayersView from "./components/pages/PlayersView";
import PlayersImport from "./components/pages/PlayersImport";

function App() {
	return (
		<div className="container">
			<Router>
				<div>
					<div className="App-header py-3"/>
					<nav className="navbar navbar-expand-lg navbar-light bg-light">
						<button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
								aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
							<span className="navbar-toggler-icon"></span>
						</button>
						<div className="collapse navbar-collapse" id="navbarNav">
							<ul className="navbar-nav">
								<li className="nav-item active">
									<Link className="nav-link font-weight-bold" to="/">Athletes</Link>
								</li>
								<li className="nav-item">
									<Link className="nav-link font-weight-bold" to="/import-players">Upload Athletes</Link>
								</li>
							</ul>
						</div>
					</nav>
				</div>
				<div className="container-fluid px-4 py-4 bg-white border-top">
					<Switch>
						<Route exact path="/" render={(props) => <PlayersList {...props} /> } />
						<Route exact path="/players/:id" render={(props) => <PlayersView {...props} /> } />
						<Route path="/import-players" render={(props) => <PlayersImport {...props} /> } />
					</Switch>
				</div>
			</Router>
		</div>
	);
}

export default App;
