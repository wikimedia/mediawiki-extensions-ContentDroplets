<?php

namespace MediaWiki\Extension\ContentDroplets\MCP;

use MediaWiki\Extension\WikiMCPTools\ExecutionHandler\DirectExecution;
use MediaWiki\Extension\WikiMCPTools\FieldSchema;
use MediaWiki\Extension\WikiMCPTools\IMcpTool;
use MediaWiki\Extension\WikiMCPTools\IMcpToolExecutionHandler;
use MediaWiki\Extension\WikiMCPTools\ToolAnnotations;
use MediaWiki\Extension\WikiMCPTools\ToolDefinition;

class GetDroplets implements IMcpTool {

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'getDroplets';
	}

	/**
	 * @return ToolDefinition
	 */
	public function getDefinition(): ToolDefinition {
		$inputSchema = ( new FieldSchema() )
			->addString( 'category', 'Only droplets in this category, e.g. "content", "data" or "navigation"' );

		$dropletSchema = ( new FieldSchema() )
			->addString( 'key', 'Droplet key' )
			->addString( 'name', 'Name shown in the editor' )
			->addString( 'description', 'What the droplet does' )
			->add( 'categories', 'Categories of the droplet', [
				'type' => 'array', 'items' => [ 'type' => 'string' ]
			] )
			->addString( 'content', 'Wikitext that adds the droplet to a page' )
			->setRequired( [ 'key', 'name', 'description', 'categories', 'content' ] );

		$outputSchema = ( new FieldSchema() )
			->addArray( 'droplets', 'Droplets of this wiki', $dropletSchema )
			->setRequired( [ 'droplets' ] );

		return new ToolDefinition(
			'Get droplets',
			'List the droplets of the visual editor: ready-made blocks such as a checklist, a list of subpages or'
				. ' the number of users. To add one to a page, put its "content" into the page text with editPage'
				. ' and fill in the values it asks for.',
			$inputSchema,
			$outputSchema,
			new ToolAnnotations( readOnly: true )
		);
	}

	/**
	 * @return IMcpToolExecutionHandler
	 */
	public function getExecutionMethod(): IMcpToolExecutionHandler {
		return new DirectExecution( [
			'class' => GetDropletsExecution::class,
			'services' => [ 'ContentDropletsProvider' ]
		] );
	}
}
