// Register models for droplets, so that VE recognizes them on load
// At alter point, full droplet data will be loaded, which will register inspectors
// Reason it is not done here is messages, which cannot be loaded through load.php entrypoint
const definitions = require( './dropletDefinitions.json' );

for ( const key in definitions ) {
	// eslint-disable-next-line no-prototype-builtins
	if ( !definitions.hasOwnProperty( key ) ) {
		continue;
	}
	let config = definitions[ key ];
	config.key = key;
	config.preload = true;
	const dropletClass = ext.contentdroplets.registry.lookup( key );
	if ( !dropletClass ) {
		if ( config.content.indexOf( 'target' ) !== -1 ) {
			new ext.contentdroplets.object.TransclusionDroplet( config );
			return;
		}
		new ext.contentdroplets.object.Droplet( config );
	} else {
		// eslint-disable-next-line new-cap
		new dropletClass( config );
	}
}
