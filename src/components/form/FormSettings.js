import React, { useCallback, useMemo } from 'react';
import { useFormik } from 'formik';
import * as yup from 'yup';
import { Stack, Box, Button } from '@chakra-ui/core';
import pathOr from 'ramda/src/pathOr';
import isEmpty from 'ramda/src/isEmpty';
import compose from 'ramda/src/compose';
import prop from 'ramda/src/prop';
import not from 'ramda/src/not';
import equals from 'ramda/src/equals';
import filter from 'ramda/src/filter';
import allPass from 'ramda/src/allPass';
import curry from 'ramda/src/curry';
import map from 'ramda/src/map';
import otherwise from 'ramda/src/otherwise';
import andThen from 'ramda/src/andThen';
import partial from 'ramda/src/partial';
import __ from 'ramda/src/__';

const { __: __i18n } = wp.i18n;

import SelectField from '../SelectField';
import Notice from '../Notice';
import api from '../../utils/api';

const redirectPageName = 'wflu_redirect_page_option';
const redirectPageAfterLoginName = 'wflu_redirect_page_after_login_option';

const validationSchema = yup.object().shape( {
	[ redirectPageName ]: yup
		.string()
		.required( __i18n( 'Required field', 'woo-for-logged-in-users' ) ),
	[ redirectPageAfterLoginName ]: yup
		.string()
		.required( __i18n( 'Required field', 'woo-for-logged-in-users' ) ),
} );

const getValue = prop( 'value' );

const isNotPageId = ( id ) => compose( not, equals( Number( id ) ), getValue );

const isNotShopPageId = isNotPageId( wfluSettings.shopPageId );
const isNotCartPageId = isNotPageId( wfluSettings.cartPageId );
const isNotCheckoutPageId = isNotPageId( wfluSettings.checkoutPageId );

const pagesAllowedToLoggedOutUsers = filter(
	allPass( [ isNotShopPageId, isNotCheckoutPageId, isNotCartPageId ] )
);

const createMessage = curry( ( type, message ) => ( {
	type,
	message,
} ) );

const getMessageError = compose(
	createMessage( 'error' ),
	pathOr(
		__i18n(
			'Something went wrong. Settings did not update.',
			'woo-for-logged-in-users'
		),
		[ 'response', 'data', 'message' ]
	)
);

const getMessageSuccess = () => createMessage( 'success', 'Settings updated' );

const prepareData = map( getValue );

const saveSettings = compose(
	otherwise( getMessageError ),
	andThen( getMessageSuccess ),
	partial( api.post, [ 'wflu/v1/settings' ] ),
	prepareData
);

const setMessage = curry( ( message, fnSet ) => fnSet( message ) );

function ButtonSaveChanges( props ) {
	return (
		<Button
			type="submit"
			variant="unstyled"
			className="button button-primary"
			width="fit-content"
			fontWeight="normal"
			height="auto"
			loadingText="Saving..."
			style={ { display: 'inline-flex' } }
			{ ...props }
		>
			{ __i18n( 'Save Changes', 'woo-for-logged-in-users' ) }
		</Button>
	);
}

export default function FormSettings( { data, defaultOptions } ) {
	const formik = useFormik( {
		initialValues: {
			[ redirectPageName ]: pathOr( '', [ redirectPageName ], data ),
			[ redirectPageAfterLoginName ]: pathOr(
				'',
				[ redirectPageAfterLoginName ],
				data
			),
		},
		validationSchema,
		onSubmit: ( values, { setStatus } ) =>
			compose(
				andThen( setMessage( __, setStatus ) ),
				saveSettings
			)( values ),
	} );

	const {
		handleSubmit,
		values,
		touched,
		errors,
		isSubmitting,
		status,
		setStatus,
		setFieldValue,
		setFieldTouched,
	} = formik;

	const onChange = useCallback(
		( nameField, value ) => {
			if ( value ) {
				setFieldValue( nameField, value, true );
			} else {
				setFieldValue( nameField, '', true );
			}
		},
		[ setFieldValue ]
	);

	const isInvalid = useCallback(
		( nameField ) => {
			return errors[ `${ nameField }` ] && touched[ `${ nameField }` ];
		},
		[ errors, touched ]
	);

	const hasError = useMemo( () => ! isEmpty( errors ), [ errors ] );

	const clearMessage = useCallback( () => setStatus( null ), [ setStatus ] );

	const onFormSubmit = useCallback( compose( clearMessage, handleSubmit ), [
		clearMessage,
		handleSubmit,
	] );

	const defaultPages = useMemo( () => defaultOptions, [ defaultOptions ] );
	const defaultPagesToLoggedOutUsers = useMemo(
		() => pagesAllowedToLoggedOutUsers( defaultOptions ),
		[ defaultOptions ]
	);

	return (
		<>
			{ status && (
				<Notice status={ status.type } onDismiss={ clearMessage }>
					{ status.message }
				</Notice>
			) }
			<form onSubmit={ onFormSubmit }>
				<Stack spacing={ 4 } width="300px" marginY="30px">
					<Box>
						<SelectField
							name={ redirectPageName }
							label={ __i18n(
								'Redirect to page',
								'woo-for-logged-in-users'
							) }
							value={ values[ redirectPageName ] }
							loggedOutUsers
							defaultOptions={ defaultPagesToLoggedOutUsers }
							onChange={ onChange }
							onBlur={ () => setFieldTouched( redirectPageName ) }
							isInvalid={ isInvalid( redirectPageName ) }
							erroMessage={ errors[ redirectPageName ] }
						/>
					</Box>

					<SelectField
						name={ redirectPageAfterLoginName }
						label={ __i18n(
							'After login redirect to page',
							'woo-for-logged-in-users'
						) }
						value={ values[ redirectPageAfterLoginName ] }
						defaultOptions={ defaultPages }
						onChange={ onChange }
						onBlur={ () =>
							setFieldTouched( redirectPageAfterLoginName )
						}
						isInvalid={ isInvalid( redirectPageAfterLoginName ) }
						erroMessage={ errors[ redirectPageAfterLoginName ] }
					/>
				</Stack>
				<ButtonSaveChanges
					isLoading={ isSubmitting }
					isDisabled={ isSubmitting || hasError }
				/>
			</form>
		</>
	);
}
