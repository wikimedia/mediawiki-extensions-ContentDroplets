// Register models for droplets, so that VE recognizes them on load
// At later point, full droplet data will be loaded, which will register inspectors
// Reason it is not done here is messages, which cannot be loaded through load.php entrypoint
const definitions = require( './dropletDefinitions.json' );

function preload() {
	for ( const key in definitions ) {
		// eslint-disable-next-line no-prototype-builtins
		if ( !definitions.hasOwnProperty( key ) ) {
			continue;
		}
		const config = definitions[ key ];
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
}

if ( mw.loader.getState( 'ext.bluespice.visualEditorConnector.tags' ) === 'registered' ) {
	// If BlueSpiceVisualEditorConnector is enabled (and not yet loaded), load it first
	mw.loader.using( 'ext.bluespice.visualEditorConnector.tags', function() {
		preload();
	} );
} else {
	preload();
}
