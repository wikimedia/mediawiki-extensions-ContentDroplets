<?php

namespace MediaWiki\Extension\ContentDroplets\Hook;

interface ContentDropletsGetDropletsHook {

	/**
	 * Add droplets dynamically
	 * @param array &$droplets must have a string key and value must be instance of IDropletDescription
	 * @return void
	 */
	public function onContentDropletsGetDroplets( array &$droplets ): void;
}
