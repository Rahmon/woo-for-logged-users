import axios from 'axios';
import get from 'lodash/get';

const api = axios.create({
  baseURL: get(wfluSettings, 'restURL', ''),
});

export default api;
