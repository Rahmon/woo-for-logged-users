import { __ as __i18n } from '@wordpress/i18n';
import { Box } from '@chakra-ui/core';

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
