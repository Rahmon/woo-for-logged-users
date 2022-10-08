import { render } from '@wordpress/element';
import { ThemeProvider, CSSReset } from '@chakra-ui/core';
import or from 'ramda/src/or';
import and from 'ramda/src/and';

import useDataApi from './hooks/useDataApi';
import formatPagesObj from './utils/formatPagesObj';
import ErrorLoadSettings from './components/ErrorLoadSettings';
import Loading from './components/Loading';
import FormSettings from './components/form/FormSettings';

import { __ as __i18n } from '@wordpress/i18n';

function App() {
	const {
		data: settings,
		loading: loadingSettings,
		error: errorSettings,
	} = useDataApi( `wflu/v1/settings` );
	const {
		data: defaultPages,
		loading: loadingDefaultPages,
		error: errorDefaultPages,
	} = useDataApi( 'wp/v2/pages?per_page=5' );

	const loading = or( loadingSettings, loadingDefaultPages );
	const error = or( errorSettings, errorDefaultPages );
	const data = and( settings, defaultPages );

	return (
		<ThemeProvider>
			<CSSReset />

			<div className="wrap">
				<h1>
					{ __i18n(
						'WooCommerce for logged-in users',
						'woo-for-logged-in-users'
					) }
				</h1>

				{ error && <ErrorLoadSettings /> }

				{ ! error && loading && <Loading /> }

				{ ! error && ! loading && data && (
					<FormSettings
						data={ settings }
						defaultOptions={ formatPagesObj( defaultPages ) }
					/>
				) }
			</div>
		</ThemeProvider>
	);
}

render( <App />, document.getElementById( 'wflu-admin' ) );
