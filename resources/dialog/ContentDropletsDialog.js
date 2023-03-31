ext.contentdroplets.ui.ContentDropletsDialog = function () {
	// Parent constructor
	ext.contentdroplets.ui.ContentDropletsDialog.super.apply( this, arguments );

	this.$element.addClass( 'contentdroplets-dialog' );
};

OO.inheritClass( ext.contentdroplets.ui.ContentDropletsDialog, OO.ui.ProcessDialog );

ext.contentdroplets.ui.ContentDropletsDialog.static.name = 'contentdroplet-dialog';

ext.contentdroplets.ui.ContentDropletsDialog.static.title = mw.message( 'contentdroplet-droplets-dialog-title' ).plain();

ext.contentdroplets.ui.ContentDropletsDialog.static.size = 'larger';

ext.contentdroplets.ui.ContentDropletsDialog.static.padded = true;

ext.contentdroplets.ui.ContentDropletsDialog.static.scrollable = false;

ext.contentdroplets.ui.ContentDropletsDialog.static.actions = [
	{
		title: mw.message( 'contentdroplet-dialog-action-close' ).plain(),
		icon: 'close',
		flags: 'safe'
	},
	{
		label: mw.message( 'contentdroplet-dialog-action-insert' ).plain(),
		action: 'insert',
		flags: [ 'primary', 'progressive' ]
	}
];

ext.contentdroplets.ui.ContentDropletsDialog.prototype.initialize = function () {
	// Parent method
	ext.contentdroplets.ui.ContentDropletsDialog.super.prototype.initialize.call( this );
	this.pushPending();
	this.content = new ext.contentdroplets.ui.ContentDropletPanel();
	this.content.connect( this, {
		dropletsAdded: function () {
			this.popPending();
			this.actions.setAbilities( { insert: false } );
		},
		dropletSelected: function ( selected ) {
			this.actions.setAbilities( { insert: !!selected } );
		}
	} );
	this.content.init();
	this.$body.append( this.content.$element );
};

ext.contentdroplets.ui.ContentDropletsDialog.prototype.getActionProcess = function ( action ) {
	var droplet, content, command;
	if ( action === 'insert' ) {
		return new OO.ui.Process( function () {
			droplet = this.content.selectedDroplet.getDropletObject();
			if ( droplet.getVeInsertCommand() &&
				ve.ui.commandRegistry.lookup( droplet.getVeInsertCommand() ) ) {
				ve.init.target.getSurface().executeCommand( droplet.getVeInsertCommand() );
			} else {
				content = droplet.getContent();
				command = new ve.ui.Command( 'dropletinsert', 'content', 'insert', {
					// Content to insert | do not annotate | collapse to end
					args: [ content, false, true ],
					supportedSelections: [ 'linear' ]
				} );
				ve.ui.wikitextCommandRegistry.register( command );
				ve.init.target.getSurface().executeCommand( 'dropletinsert' );
				ve.ui.wikitextCommandRegistry.unregister( 'dropletinsert' );
			}

			this.close( { action: action } );
		}, this );
	}
	return ext.contentdroplets.ui.ContentDropletsDialog.super.prototype.getActionProcess.call(
		this, action
	);
};

ext.contentdroplets.ui.ContentDropletsDialog.prototype.getBodyHeight = function () {
	if ( !this.$errors.hasClass( 'oo-ui-element-hidden' ) ) {
		return this.$element.find( '.oo-ui-processDialog-errors' )[ 0 ].scrollHeight;
	}
	return 480;
};

ext.contentdroplets.ui.ContentDropletsDialog.prototype.getSetupProcess = function ( data ) {
	return ext.contentdroplets.ui.ContentDropletsDialog.super.prototype.getSetupProcess.call(
		this, data
	).next( function () {
		this.content.resetSearch();
	}, this );
};

/* Registration */
ve.ui.windowFactory.register( ext.contentdroplets.ui.ContentDropletsDialog );
