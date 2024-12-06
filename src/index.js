/* global wfluSettings */

import {
	Spinner,
	ComboboxControl,
	Flex,
	Button,
	Notice,
	Disabled,
} from '@wordpress/components';
import { useState, render } from '@wordpress/element';
import { useEntityRecords } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

import './style.scss';

const { shopPageId, cartPageId, checkoutPageId, settings } = wfluSettings;

function App() {
	const [ savedData, setSavedData ] = useState( {
		redirectToPage: settings.wflu_redirect_page_option,
		afterLoginRedirectToPage:
			settings.wflu_redirect_page_after_login_option,
	} );

	const [ data, setData ] = useState( {
		redirectToPage: settings.wflu_redirect_page_option.value,
		afterLoginRedirectToPage:
			settings.wflu_redirect_page_after_login_option.value,
	} );

	const [ isSaving, setIsSaving ] = useState( false );

	const [ notice, setNotice ] = useState();

	let SelectRedirectToPage = (
		<Select
			label={ __( 'Redirect to page', 'woo-for-logged-in-users' ) }
			queryArgs={ {
				exclude: [ shopPageId, cartPageId, checkoutPageId ],
			} }
			value={ data.redirectToPage }
			onChange={ ( value ) =>
				setData( { ...data, redirectToPage: value } )
			}
			savedData={ savedData.redirectToPage }
			style={ { marginBottom: '15px', marginTop: '25px' } }
		/>
	);

	let SelectAfterLoginRedirectToPage = (
		<Select
			label={ __(
				'After login redirect to page',
				'woo-for-logged-in-users'
			) }
			value={ data.afterLoginRedirectToPage }
			onChange={ ( value ) =>
				setData( {
					...data,
					afterLoginRedirectToPage: value,
				} )
			}
			savedData={ savedData.afterLoginRedirectToPage }
			style={ { marginBottom: '15px' } }
		/>
	);

	if ( isSaving ) {
		SelectRedirectToPage = <Disabled>{ SelectRedirectToPage }</Disabled>;
		SelectAfterLoginRedirectToPage = (
			<Disabled>{ SelectAfterLoginRedirectToPage }</Disabled>
		);
	}

	const onSubmit = ( event ) => {
		event.preventDefault();

		setIsSaving( true );
		setNotice( null );

		apiFetch( {
			path: 'wflu/v1/settings',
			method: 'POST',
			data: {
				wflu_redirect_page_option: data.redirectToPage,
				wflu_redirect_page_after_login_option:
					data.afterLoginRedirectToPage,
			},
		} )
			.then( ( response ) => {
				setSavedData( {
					redirectToPage: response.wflu_redirect_page_option,
					afterLoginRedirectToPage:
						response.wflu_redirect_page_after_login_option,
				} );

				setData( {
					redirectToPage: response.wflu_redirect_page_option.value,
					afterLoginRedirectToPage:
						response.wflu_redirect_page_after_login_option.value,
				} );

				setNotice( {
					status: 'success',
					message: __(
						'Settings updated.',
						'woo-for-logged-in-users'
					),
				} );
			} )
			.catch( ( response ) => {
				setNotice( {
					status: 'error',
					message:
						response?.message ||
						__(
							'Something went wrong. The settings were not updated.',
							'woo-for-logged-in-users'
						),
				} );
			} )
			.finally( () => {
				setIsSaving( false );
			} );
	};

	return (
		<div>
			<h1>
				{ __(
					'WooCommerce for logged-in users',
					'woo-for-logged-in-users'
				) }
			</h1>

			<div style={ { width: '440px' } }>
				{ notice && (
					<div
						style={ { marginBottom: '.67em', marginRight: '44px' } }
					>
						<Notice
							status={ notice.status }
							onRemove={ () => {
								setNotice( null );
							} }
						>
							{ notice.message }
						</Notice>
					</div>
				) }

				<form style={ { width: '440px' } } onSubmit={ onSubmit }>
					{ SelectRedirectToPage }
					{ SelectAfterLoginRedirectToPage }

					<Button
						type="submit"
						variant="primary"
						text={ __( 'Save', 'woo-for-logged-in-users' ) }
						disabled={ isSaving }
					/>

					{ isSaving && <Spinner /> }
				</form>
			</div>
		</div>
	);
}

function Select( { label, value, queryArgs, onChange, savedData, style } ) {
	const [ searchTerm, setSearchTerm ] = useState( '' );

	const query = { per_page: 4, ...queryArgs };

	if ( searchTerm ) {
		query.search = searchTerm;
		query.per_page = 5;
	}

	const { hasResolved, records: pages } = useEntityRecords(
		'postType',
		'page',
		query
	);

	const getOptions = () => {
		if ( ! hasResolved ) {
			return [ savedData ];
		}

		if ( ! pages?.length ) {
			return [ savedData ];
		}

		if ( ! searchTerm ) {
			return [
				savedData,
				...pages.map( ( page ) => ( {
					value: page.id,
					label: page.title.raw,
				} ) ),
			];
		}

		return pages.map( ( page ) => ( {
			value: page.id,
			label: page.title.raw,
		} ) );
	};

	return (
		<Flex style={ style }>
			<span style={ { width: '100%' } }>
				<ComboboxControl
					label={ label }
					value={ value }
					onChange={ ( newValue ) => {
						if ( ! newValue ) {
							return;
						}

						onChange( newValue );
					} }
					onFilterValueChange={ ( term ) => {
						setSearchTerm( term );
					} }
					options={ getOptions() }
					help={ __(
						'Type to search for pages',
						'woo-for-logged-in-users'
					) }
					allowReset={ false }
				/>
			</span>
			<Spinner
				style={ {
					visibility: hasResolved ? 'hidden' : 'initial',
				} }
			/>
		</Flex>
	);
}

window.addEventListener(
	'load',
	function () {
		render( <App />, document.querySelector( '#wflu-admin' ) );
	},
	false
);
