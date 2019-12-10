import React, {Component} from "react";
import ApiService from "../../api/ApiService";
import {Link} from "react-router-dom";

class PlayersList extends Component{
	state = {
		players: []
	};

	componentDidMount() {
		let endpoint = 'players';
		ApiService.apiGet(endpoint)
			.then(res => {
				let players = res.data.data;
				this.setState({ players });
			})
	}

	render() {
		return (
			<div>
				<h3 className="py-4 font-weight-bold">Athletes</h3>

				<table className="table table-bordered borderless">
					<thead className="thead-light">
					<tr>
						<th scope="col">Name</th>
						<th scope="co" className="align-content-center">Age</th>
						<th scope="col" className="align-content-center">Location</th>
						<th scope="col" className="align-content-right">Actions</th>
					</tr>
					</thead>
					<tbody>

						{this.state.players.map(function(player, index) {
							return (
								<tr key={index}>
									<td>{player.name}</td>
									<td>{player.age}</td>
									<td>{player.city + ', ' + player.province }</td>
									<td className="actions">
										<Link to={`/players/${player.id}`}>View</Link>
										<a>Delete</a>
									</td>
								</tr>
							)
						})}

					</tbody>
				</table>
			</div>
		);
	}
}

export default PlayersList;
