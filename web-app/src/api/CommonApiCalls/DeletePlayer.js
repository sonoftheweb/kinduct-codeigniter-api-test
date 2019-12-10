import ApiService from "../ApiService";
import React from "react";
import {Redirect} from "react-router-dom";

class DeletePlayer {
	static deletePlayer(playerId) {
		if (!playerId) {
			// alert that there is no player id here
		}

		ApiService.apiDelete('players/' + id)
			.then(res => {
				// there is no need to get anything when delete is done.
				// Just re-direct to the list page
				return <Redirect to='/' />
			});
	}
}

export default DeletePlayer;
