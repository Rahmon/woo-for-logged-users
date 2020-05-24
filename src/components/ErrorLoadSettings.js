import React from 'react';
import { Box } from '@chakra-ui/core';

const { __: __i18n } = wp.i18n;

export default function ErrorLoadSettings() {
	return (
		<Box marginTop="30px" fontSize="16px">
			{ __i18n(
				"Something went wrong. It wasn't possible to load the settings.",
				'woo-for-logged-in-users'
			) }
		</Box>
	);
}
