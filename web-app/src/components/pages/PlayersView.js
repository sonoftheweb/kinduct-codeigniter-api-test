import React, {Component} from "react";
import ApiService from "../../api/ApiService";
import DeletePlayer, from "../../api/CommonApiCalls/DeletePlayer";
import {Redirect} from "react-router-dom";

class PlayersView extends Component {
	state = {
		player: {},
		endpoint: 'players/' + this.props.match.params.id,
		loading: false
	};

	componentDidMount() {
		ApiService.apiGet(this.state.endpoint)
			.then(res => {
				let player = res.data.data;
				this.setState({ player });
			});
	}

	render() {
		return (
			<div>
				<h3 className="py-4 font-weight-bold">{this.state.player.name}</h3>

				<table className="table table-bordered player-table">
					<tbody>
						<tr>
							<th scope="col" className="bg-light" width="200px">AGE</th>
							<td scope="col" className="align-content-center">{this.state.player.age}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">CITY</th>
							<td scope="co" className="align-content-center">{this.state.player.city}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">PROVINCE</th>
							<td scope="co" className="align-content-center">{this.state.player.province}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">COUNTRY</th>
							<td scope="co" className="align-content-center">{this.state.player.country}</td>
						</tr>
					</tbody>
				</table>

				<button disabled={this.state.loading} onClick={DeletePlayer.deletePlayer(this.state.player.id)} type="button" className="btn btn-primary mt-5 font-weight-bold px-4">Delete</button>
			</div>
		);
	}

	// Of course you could do this, but then again you'd have to do the same thing on the list page. Why not make a class to handle that and call it in when you need?
	/*delete() {
		this.state.loading = true;
		ApiService.apiDelete(this.state.endpoint)
			.then(res => {
				this.state.loading = false;
				// there is no need to get anything when delete is done.
				// Just re-direct to the list page
				return <Redirect to='/' />
			});
	}*/
}

export default PlayersView;
