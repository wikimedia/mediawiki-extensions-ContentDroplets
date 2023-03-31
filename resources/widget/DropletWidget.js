ext.contentdroplets.ui.DropletWidget = function ( cfg ) {
	ext.contentdroplets.ui.DropletWidget.parent.call( this, cfg );

	this.droplet = cfg.droplet;
	this.pageId = cfg.id || '$';
	this.module = cfg.module || '';

	OO.EventEmitter.call( this );

	this.iconElement = new OO.ui.Widget( {
		content: $( '<div></div>' ),
		classes: [ 'icon-droplet', this.droplet.getIcon() ] } );

	this.content = new OO.ui.Widget( {
		padded: true,
		expanded: false,
		classes: [ 'content-droplet' ]
	} );
	this.setupContent();

	this.container = new OO.ui.HorizontalLayout( {
		padded: true,
		items: [
			this.iconElement,
			this.content
		]
	} );
	this.$element.addClass( 'droplet' );
	this.$element.append( this.container.$element );

	this.$element.on( 'click', this.select.bind( this ) );
};

OO.inheritClass( ext.contentdroplets.ui.DropletWidget, OO.ui.Widget );
OO.mixinClass( ext.contentdroplets.ui.DropletWidget, OO.EventEmitter );

ext.contentdroplets.ui.DropletWidget.prototype.select = function () {
	this.$element.addClass( 'droplet-selected' );
	this.emit( 'selected', this.droplet.getKey(), this );
};

ext.contentdroplets.ui.DropletWidget.prototype.deselect = function () {
	this.$element.removeClass( 'droplet-selected' );
	this.emit( 'deselected', this.droplet.getKey() );
};

ext.contentdroplets.ui.DropletWidget.prototype.getDropletObject = function () {
	return this.droplet;
};

ext.contentdroplets.ui.DropletWidget.prototype.setupContent = function () {
	this.titleWidget = new OO.ui.LabelWidget( {
		label: this.droplet.getName(),
		classes: [ 'droplet-title' ]
	} );

	this.descWidget = new OO.ui.LabelWidget( {
		label: this.droplet.getDescription(),
		classes: [ 'droplet-text' ]
	} );

	this.fieldsetLayout = new OO.ui.FieldsetLayout( {
		items: [
			this.titleWidget,
			this.descWidget
		]
	} );

	this.content.$element.append( this.fieldsetLayout.$element );
};
