import axios from 'axios';

const baseUrl = 'http://localhost/kinduct-codeigniter-api-test/api/';

class ApiService {
	static apiPost(params, data) {
		return axios.post(baseUrl + params, data);
	}

	static apiGet(params) {
		return axios.get(baseUrl + params);
	}

	static apiDelete(params) {
		return axios.delete(baseUrl + params);
	}
}

export default ApiService;
