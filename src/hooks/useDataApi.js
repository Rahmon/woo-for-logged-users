import { useEffect, useState, useReducer  } from '@wordpress/element';

import api from '../utils/api';

const dataFetchReducer = (state, action) => {
  switch (action.type) {
    case 'FETCH_INIT':
      return {
        ...state,
        loading: true,
        error: false,
      };
    case 'FETCH_SUCCESS':
      return {
        ...state,
        loading: false,
        error: false,
        data: action.payload,
      };
    case 'FETCH_FAILURE':
      return {
        ...state,
        loading: false,
        error: action.error || true,
      };
    default:
      throw new Error();
  }
};

const useDataApi = (initialUrl, initialData) => {
  const [url, setUrl] = useState(initialUrl);

  const [state, dispatch] = useReducer(dataFetchReducer, {
    loading: false,
    error: false,
    data: initialData,
  });

  useEffect(() => {
    let didCancel = false;

    if (url) {
      const fetchData = async () => {
        dispatch({ type: 'FETCH_INIT' });
        try {
          const result = await api.get(url);
          if (!didCancel) {
            dispatch({ type: 'FETCH_SUCCESS', payload: result.data });
          }
        } catch (error) {
          if (!didCancel) {
            dispatch({ type: 'FETCH_FAILURE', error: error });
          }
        }
      };
      fetchData();
    }

    return () => {
      didCancel = true;
    };
  }, [url]);

  return { ...state, setUrl };
};

export default useDataApi;
