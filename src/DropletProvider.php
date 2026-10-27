<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\ContentDroplets;

use Exception;

class DropletProvider {
	/** @var IDropletSource[] */
	private $sources;
	/** @var IDropletDescription[] */
	private $droplets;
	/** @var string[] */
	private $excludedDroplets;

	/**
	 * @param IDropletSource $sources
	 * @param string[] $excludedDroplets
	 */
	public function __construct( array $sources, array $excludedDroplets = [] ) {
		$this->sources = $sources;
		$this->excludedDroplets = $excludedDroplets;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getDroplets(): array {
		$this->assertSourcesLoaded();
		$this->assertDropletVisibility();

		return $this->droplets;
	}

	/**
	 * @return void
	 */
	private function assertSourcesLoaded(): void {
		if ( $this->droplets === null ) {
			$this->droplets = [];
			foreach ( $this->sources as $key => $source ) {
				$this->droplets = array_merge( $this->droplets, $source->getDroplets() );
			}
			$this->droplets = array_diff_key(
				$this->droplets,
				array_fill_keys( $this->excludedDroplets, true )
			);
		}
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function assertDropletVisibility(): void {
		if ( $this->droplets === null ) {
			throw new Exception( "Droplet sources must be loaded first" );
		}

		$this->droplets = array_filter(
			$this->droplets,
			static fn ( IDropletDescription $droplet ) => $droplet->listDroplet()
		);
	}
}
