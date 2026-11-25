<?php

namespace MediaWiki\Extension\ContentDroplets\Hook;

use MediaWiki\Extension\ContentDroplets\MCP\GetDroplets;
use MediaWiki\Hook\MediaWikiServicesHook;
use MediaWiki\MediaWikiServices;

/**
 * Registers the getDroplets tool in WikiMCPTools when that extension is loaded
 */
class RegisterMCPTools implements MediaWikiServicesHook {

	/**
	 * @param MediaWikiServices $services
	 * @return void
	 */
	public function onMediaWikiServices( $services ): void {
		// MCP.PageResolver came with the WikiMCPTools version that has ToolAnnotations
		if ( !$services->hasService( 'MCP.ToolRegistry' ) || !$services->hasService( 'MCP.PageResolver' ) ) {
			return;
		}
		$services->getService( 'MCP.ToolRegistry' )->registerTool( new GetDroplets() );
	}
}
