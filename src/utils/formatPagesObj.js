import map from 'ramda/src/map';

function formatOptions( data ) {
	return {
		value: data.id,
		label: data.title.rendered,
	};
}

const formatPagesObj = map( formatOptions );

export default formatPagesObj;
