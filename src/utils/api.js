import axios from 'axios';
import {get} from 'lodash-es';

const api = axios.create( {
	baseURL: get( wfluSettings, 'restURL', '' ),
	headers: { 'X-WP-Nonce': get( wfluSettings, 'nonce', '' ) },
} );

export default api;
