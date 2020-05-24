import React from 'react';

const { __: __i18n } = wp.i18n;

export default function Notice( { status, onDismiss, children } ) {
	return (
		<div className={ `notice notice-${ status } is-dismissible` }>
			<p>{ children }</p>
			<button
				type="button"
				className="notice-dismiss"
				onClick={ onDismiss }
			>
				<span className="screen-reader-text">
					{ __i18n(
						'Dismiss this notice.',
						'woo-for-logged-in-users'
					) }
				</span>
			</button>
		</div>
	);
}
