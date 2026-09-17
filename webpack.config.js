const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'edit-project': path.resolve( __dirname, 'src/edit-project.js' ),
	},
};
