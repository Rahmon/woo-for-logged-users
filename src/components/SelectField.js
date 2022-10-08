import { FormControl, FormLabel, FormErrorMessage } from '@chakra-ui/core';
import partial from 'ramda/src/partial';
import curry from 'ramda/src/curry';
import __ from 'ramda/src/__';
import map from 'ramda/src/map';

import api from '../utils/api';
import formatPagesObj from '../utils/formatPagesObj.js';
import Select from './Select';

function getPages( term, exclude ) {
	let excludeQuery = '';
	if ( exclude ) {
		excludeQuery = `exclude=${ wfluSettings.shopPageId },${ wfluSettings.cartPageId },${ wfluSettings.checkoutPageId }&`;
	}

	return api
		.get( `wp/v2/pages?${ excludeQuery }per_page=5&search=${ term }` )
		.then( ( result ) => formatPagesObj( result.data ) );
}

const getPagesCurried = curry( getPages );

function Label( { children, ...rest } ) {
	return (
		<FormLabel color="#23282d" fontSize="sm" { ...rest }>
			{ children }
		</FormLabel>
	);
}

export default function SelectField( {
	name,
	label,
	value,
	defaultOptions,
	loggedOutUsers,
	onChange,
	onBlur,
	isInvalid,
	erroMessage,
} ) {
	return (
		<FormControl isInvalid>
			<Label htmlFor={ name }>{ label }</Label>

			<Select
				name={ name }
				value={ value }
				defaultOptions={ defaultOptions }
				onChange={ partial( onChange, [ name ] ) }
				onBlur={ onBlur }
				loadOptions={ getPagesCurried( __, loggedOutUsers ) }
			/>
			<FormErrorMessage visibility={ ! isInvalid ? 'hidden' : undefined }>
				{ erroMessage }
			</FormErrorMessage>
		</FormControl>
	);
}
