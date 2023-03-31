<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\ContentDroplets\DropletSource;

use InvalidArgumentException;
use JsonContent;
use MediaWiki\Extension\ContentDroplets\Droplet\GenericDroplet;
use MediaWiki\Extension\ContentDroplets\IDropletSource;
use WikiPage;

class WikiPageSource implements IDropletSource {
	/** @var WikiPage */
	private $wikipage;

	/**
	 * @param WikiPage $wikiPage
	 */
	public function __construct( WikiPage $wikiPage ) {
		$this->wikipage = $wikiPage;
	}

	/**
	 * @inheritDoc
	 */
	public function getDroplets(): array {
		if ( !$this->wikipage->getTitle()->exists() ) {
			return [];
		}

		$pageTitle = $this->wikipage->getTitle()->getPrefixedText();
		$content = $this->wikipage->getContent();
		if ( !( $content instanceof JsonContent ) ) {
			throw new InvalidArgumentException(
				"Page \"$pageTitle\" must have JSON content model"
			);
		}
		$status = $content->getData();
		if ( !$status->isOK() ) {
			throw new InvalidArgumentException( "Invalid content on \"$pageTitle\"" );
		}

		$json = $status->getValue();
		return $this->makeDroplets( $json );
	}

	/**
	 * @param array $json
	 * @return array
	 */
	private function makeDroplets( object $json ): array {
		$droplets = [];
		foreach ( $json as $key => $data ) {
			$droplets[$key] = new GenericDroplet(
				$data->name,
				$data->description,
				$data->icon,
				$data->content,
				$data->categories,
				$data->rlModules
			);
		}

		return $droplets;
	}
}
