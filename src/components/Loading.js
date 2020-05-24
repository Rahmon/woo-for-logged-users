import React from 'react';

import { Flex, Spinner } from '@chakra-ui/core';

export default function Loading() {
	return (
		<Flex marginTop="30px">
			<Spinner speed="0.65s" label="Loading" />
		</Flex>
	);
}
