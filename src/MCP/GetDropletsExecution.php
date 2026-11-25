<?php

namespace MediaWiki\Extension\ContentDroplets\MCP;

use MediaWiki\Extension\ContentDroplets\DropletProvider;
use MediaWiki\Extension\WikiMCPTools\IMcpTool;
use MediaWiki\Extension\WikiMCPTools\IMcpToolExecutor;

class GetDropletsExecution implements IMcpToolExecutor {

	/**
	 * @param DropletProvider $provider
	 */
	public function __construct(
		private readonly DropletProvider $provider
	) {
	}

	/**
	 * @param IMcpTool $tool
	 * @param array $input
	 * @return array
	 */
	public function execute( IMcpTool $tool, array $input ): array {
		$category = trim( $input['category'] ?? '' );
		$droplets = [];
		// The provider leaves out droplets that are hidden on this wiki
		foreach ( $this->provider->getDroplets() as $key => $droplet ) {
			$categories = array_values( $droplet->getCategories() );
			if ( $category !== '' && !in_array( $category, $categories, true ) ) {
				continue;
			}
			$content = $this->toWikitext( $droplet->getContent() );
			// Droplets without content can only be added in the visual editor
			if ( trim( $content ) === '' ) {
				continue;
			}
			$droplets[] = [
				'key' => (string)$key,
				'name' => $droplet->getName()->plain(),
				'description' => $droplet->getDescription()->plain(),
				'categories' => $categories,
				'content' => $content,
			];
		}
		return [ 'droplets' => $droplets ];
	}

	/**
	 * Template and parser function droplets give their content as visual editor JSON,
	 * e.g. {"target":{"wt":"Textbox"},"params":{"boxtype":"note"}} becomes {{Textbox|boxtype=note}}
	 *
	 * @param string $content
	 * @return string
	 */
	private function toWikitext( string $content ): string {
		$data = json_decode( $content, true );
		if ( !is_array( $data ) || !is_string( $data['target']['wt'] ?? null ) ) {
			return $content;
		}
		$text = '{{' . $data['target']['wt'];
		foreach ( $data['params'] ?? [] as $name => $value ) {
			$text .= '|' . $name . '=' . ( is_scalar( $value ) ? $value : '' );
		}
		return $text . '}}';
	}
}
