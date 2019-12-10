import React, {Component} from "react";
import ApiService from "../../api/ApiService";

class PlayersImport extends Component {

	constructor(props) {
		super(props);
		this.state = {
			file: null
		};

		this.onFormSubmit = this.onFormSubmit.bind(this);
		this.fileUpload = this.fileUpload.bind(this);
		this.onChangeFile = this.onChangeFile.bind(this);

		this.inputFileRef = React.createRef();
		this.inputFileLabelRef = React.createRef();
		this.onBtnClick = this.handleBtnClick.bind(this);
	};

	handleBtnClick() {
		this.inputFileRef.current.click();
	};

	onFormSubmit = (e) => {
		e.preventDefault();
		this.fileUpload(this.state.file);
	};

	fileUpload = async (file) => {
		let uri = 'players',
			formData = new FormData(),
			config = {
				headers: {
					'content-type': 'multipart/form-data'
				}
			};

		formData.append('file', 'true');
		formData.append('players_file', file);

		await ApiService.apiPost(uri, formData, config)
			.then(res => {
				this.props.history.push('/');
			});
	};

	onChangeFile = (event) => {
		event.stopPropagation();
		event.preventDefault();
		let file = event.target.files[0];
		this.inputFileLabelRef.current.innerHTML = file.name;
		this.setState({file:file});
	};

	render() {
		return (
			<div>
				<h3 className="py-4 font-weight-bold">Upload Athletes</h3>
				<form id="uploadPlayers" onSubmit={this.onFormSubmit}>

					<div className="input-group mb-3">
						<div className="custom-file">
							<input type="file" className="custom-file-input" id="file"
								   ref={this.inputFileRef}
								   onChange={this.onChangeFile}
								   aria-describedby="inputGroupFileAddon01"/>
								<label ref={this.inputFileLabelRef} className="custom-file-label" id="fileInput" htmlFor="file"></label>
						</div>
						<button onClick={this.onBtnClick} className="btn btn-outline-primary px-4 ml-3" type="button" id="inputGroupFileAddon04">Browse</button>
					</div>

					<button type="submit" className="btn btn-primary btn-block">Upload</button>

				</form>
			</div>
		);
	}
}

export default PlayersImport;
