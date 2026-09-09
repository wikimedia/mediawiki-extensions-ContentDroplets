<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\ContentDroplets\DropletSource;

use InvalidArgumentException;
use MediaWiki\Extension\ContentDroplets\IDropletDescription;
use MediaWiki\Extension\ContentDroplets\IDropletSource;
use MediaWiki\HookContainer\HookContainer;

class HookSource implements IDropletSource {

	/**
	 * @param HookContainer $hookContainer
	 */
	public function __construct(
		private readonly HookContainer $hookContainer
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getDroplets(): array {
		$droplets = [];
		$this->hookContainer->run( 'ContentDropletsGetDroplets', [ &$droplets ] );

		foreach ( $droplets as $key => $droplet ) {
			if ( is_int( $key ) || empty( $key ) ) {
				throw new InvalidArgumentException(
					"All droplets must have a non-empty string key"
				);
			}
			if ( !( $droplet instanceof IDropletDescription ) ) {
				throw new InvalidArgumentException(
					"Invalid object spec for droplet source \"$key\""
				);
			}
		}
		return $droplets;
	}
}
