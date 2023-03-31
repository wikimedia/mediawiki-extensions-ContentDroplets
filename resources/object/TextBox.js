ext.contentdroplets.object.TextBox = function ( cfg ) {
	ext.contentdroplets.object.TextBox.parent.call( this, cfg );
};

OO.inheritClass( ext.contentdroplets.object.TextBox,
	ext.contentdroplets.object.TransclusionDroplet );

ext.contentdroplets.object.TextBox.prototype.templateMatches = function ( templateData ) {
	if ( !templateData ) {
		return false;
	}
	// eslint-disable-next-line vars-on-top
	var target = templateData.target.wt;
	return target.trim( '\n' ) === 'Textbox' &&
		'text-box-' + templateData.params.boxtype.wt === this.getKey();
};

ext.contentdroplets.object.TextBox.prototype.getFormItems = function () {
	var data = this.getDropdownData();
	return [
		{
			name: 'icon',
			label: mw.message( 'contentdroplets-droplet-text-box-show-icon-label' ).text(),
			type: 'checkbox'
		},
		{
			name: 'boxtype',
			label: mw.message( 'contentdroplets-droplet-text-box-type-label' ).text(),
			type: 'dropdown',
			options: data
		},
		{
			name: 'header',
			label: mw.message( 'contentdroplets-droplet-text-box-header-label' ).text(),
			type: 'textarea',
			rows: 2
		},
		{
			name: 'text',
			label: mw.message( 'contentdroplets-droplet-text-box-text-label' ).text(),
			type: 'textarea',
			rows: 3
		}
	];
};

ext.contentdroplets.object.TextBox.prototype.getDropdownData = function () {
	// eslint-disable-next-line no-undef
	var types = require( './textboxtypes.json' ),
		data = [], type;
	if ( types !== [] ) {
		for ( type in types ) {
			data.push( {
				data: types[ type ],
				// The following messages are used here:
				// * contentdroplets-droplet-text-box-success
				// * contentdroplets-droplet-text-box-warning
				// * contentdroplets-droplet-text-box-important
				// * contentdroplets-droplet-text-box-note
				// * contentdroplets-droplet-text-box-tip
				// * contentdroplets-droplet-text-box-neutral
				label: mw.message( 'contentdroplets-droplet-text-box-' + types[ type ] ).text()
			} );
		}
	}
	return data;
};

ext.contentdroplets.object.TextBox.prototype.modifyFormDataBeforeSubmission =
	function ( dataPromise ) {
		// Convert true/false from checkbox control, to yes/no expected by the Textbox template
		var dfd = $.Deferred();
		dataPromise.done( function ( data ) {
			data.icon = data.icon ? 'yes' : 'no';
			dfd.resolve( data );
		} ).fail( function () {
			dfd.reject( arguments );
		} );

		return dfd.promise();
	};

ext.contentdroplets.object.TextBox.prototype.getForm = function ( data ) {
	// convert yes/no to true and false for checkbox control
	if ( data.icon === 'no' ) {
		data.icon = false;
	} else {
		data.icon = true;
	}
	return ext.contentdroplets.object.TextBox.parent.prototype.getForm.call( this, data );
};

// Register all droplets that use this class
// eslint-disable-next-line no-undef, vars-on-top, no-implicit-globals
var types = require( './textboxtypes.json' ), type;
for ( type in types ) {
	ext.contentdroplets.registry.register( 'text-box-' + types[ type ], ext.contentdroplets.object.TextBox );
}
