import React, {Component} from "react";
import ApiService from "../../api/ApiService";

class PlayersView extends Component {
	state = {
		player: {}
	};

	componentDidMount() {
		if (this.props.match.params.id) {
			let uri = 'players/' + this.props.match.params.id;

			ApiService.apiGet(uri)
				.then(res => {
					let player = res.data.data;
					this.setState({ player });
				});
		}
	}

	render() {
		return (
			<div>
				<h3 className="py-4 font-weight-bold">{this.state.player.name}</h3>

				<table className="table table-bordered player-table">
					<tbody>
						<tr>
							<th scope="col" className="bg-light" width="200px">AGE</th>
							<td className="align-content-center">{this.state.player.age}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">CITY</th>
							<td className="align-content-center">{this.state.player.city}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">PROVINCE</th>
							<td className="align-content-center">{this.state.player.province}</td>
						</tr>
						<tr>
							<th scope="col" className="bg-light">COUNTRY</th>
							<td className="align-content-center">{this.state.player.country}</td>
						</tr>
					</tbody>
				</table>

				<button disabled={this.state.loading} onClick={this.delete} type="button" className="btn btn-primary mt-5 font-weight-bold px-4">Delete</button>
			</div>
		);
	}

	delete = async () => {
		this.setState({loading: true});
		let uri = 'players/' + this.props.match.params.id + '/delete';

		await ApiService.apiPost(uri)
			.then(res => {
				this.setState({loading: false});
				// there is no need to get anything when delete is done.
				// Just re-direct to the list page
				this.props.history.push('/');
			});
	}
}

export default PlayersView;
