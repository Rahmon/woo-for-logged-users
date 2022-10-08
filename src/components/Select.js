import AsyncSelect from 'react-select/async';

const height = 32;
const fontSize = 14;
const marginTop = '-2px';

const primary = '#007cba';
const neutral20 = '#7e8993';

function styleIndicator( base ) {
	return {
		...base,
		marginTop,
		height,
		paddingTop: 6,
		paddingBottom: 6,
	};
}

export default function Select( props ) {
	return (
		<AsyncSelect
			defaultOptions
			cacheOptions
			isClearable
			placeholder=""
			openMenuOnClick={ false }
			{ ...props }
			styles={ {
				singleValue: ( base ) => ( {
					...base,
					fontSize,
				} ),
				valueContainer: ( base ) => ( {
					...base,
					marginTop,
					height,
				} ),
				indicatorsContainer: ( base ) => ( {
					...base,
					height,
				} ),
				clearIndicator: styleIndicator,
				dropdownIndicator: styleIndicator,
				control: ( base ) => ( {
					...base,
					height,
					minHeight: 32,
				} ),
				input: ( base ) => ( {
					...base,
					height,
					marginTop,
					paddingTop: 0,
					marginBottom: 0,
					"input[type='text']:focus": {
						boxShadow: 'none',
					},
				} ),
			} }
			theme={ ( theme ) => {
				return {
					...theme,
					colors: {
						...theme.colors,
						primary,
						neutral20,
					},
				};
			} }
		/>
	);
}
