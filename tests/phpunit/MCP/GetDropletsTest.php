<?php

namespace MediaWiki\Extension\ContentDroplets\Tests\MCP;

use MediaWiki\Extension\ContentDroplets\DropletProvider;
use MediaWiki\Extension\ContentDroplets\Hook\RegisterMCPTools;
use MediaWiki\Extension\ContentDroplets\IDropletDescription;
use MediaWiki\Extension\ContentDroplets\MCP\GetDroplets;
use MediaWiki\Extension\ContentDroplets\MCP\GetDropletsExecution;
use MediaWiki\Extension\WikiMCPTools\ExecutionHandler\DirectExecution;
use MediaWiki\Extension\WikiMCPTools\IMcpTool;
use MediaWiki\Extension\WikiMCPTools\ToolAnnotations;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\ContentDroplets\MCP\GetDroplets
 * @covers \MediaWiki\Extension\ContentDroplets\MCP\GetDropletsExecution
 * @covers \MediaWiki\Extension\ContentDroplets\Hook\RegisterMCPTools
 */
class GetDropletsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		if ( !class_exists( ToolAnnotations::class ) ) {
			$this->markTestSkipped( 'WikiMCPTools is not installed' );
		}
	}

	/**
	 * @param string $text
	 * @return Message
	 */
	private function message( string $text ): Message {
		$message = $this->createMock( Message::class );
		$message->method( 'plain' )->willReturn( $text );
		return $message;
	}

	/**
	 * @param string $name
	 * @param string[] $categories
	 * @param string $content
	 * @return IDropletDescription
	 */
	private function droplet( string $name, array $categories, string $content ): IDropletDescription {
		$droplet = $this->createMock( IDropletDescription::class );
		$droplet->method( 'getName' )->willReturn( $this->message( $name ) );
		$droplet->method( 'getDescription' )->willReturn( $this->message( "About $name" ) );
		$droplet->method( 'getCategories' )->willReturn( $categories );
		$droplet->method( 'getContent' )->willReturn( $content );
		return $droplet;
	}

	/**
	 * @return GetDropletsExecution
	 */
	private function newExecution(): GetDropletsExecution {
		$provider = $this->createMock( DropletProvider::class );
		$provider->method( 'getDroplets' )->willReturn( [
			'checklist' => $this->droplet( 'Checklist', [ 'content', 'data' ], '<bs:checklist />' ),
			'subpages' => $this->droplet( 'Subpages', [ 'navigation' ], '<subpages />' ),
			'note' => $this->droplet( 'Note', [ 'content' ],
				'{"target":{"href":"./Template:Textbox","wt":"Textbox"},"params":{"boxtype":"note","text":""}}' ),
			'embed' => $this->droplet( 'Embed', [ 'content' ],
				'{"target":{"function":"embed","wt":"#embed:"},"params":{"lang":""}}' ),
			'layout' => $this->droplet( 'Layout', [ 'content' ], '' ),
		] );
		return new GetDropletsExecution( $provider );
	}

	public function testDefinition() {
		$tool = new GetDroplets();
		$definition = $tool->getDefinition()->jsonSerialize();
		$this->assertSame( 'getDroplets', $tool->getKey() );
		$this->assertTrue( $definition['annotations']['readOnlyHint'] );
		$this->assertInstanceOf( DirectExecution::class, $tool->getExecutionMethod() );
		$item = $definition['outputSchema']->jsonSerialize()['properties']['droplets']['items'];
		$this->assertSame(
			[ 'key', 'name', 'description', 'categories', 'content' ], array_keys( $item['properties'] )
		);
	}

	public function testListsDropletsWithContent() {
		$result = $this->newExecution()->execute( $this->createMock( IMcpTool::class ), [] );
		$this->assertSame(
			[ 'checklist', 'subpages', 'note', 'embed' ], array_column( $result['droplets'], 'key' )
		);
		$this->assertSame( [
			'key' => 'checklist',
			'name' => 'Checklist',
			'description' => 'About Checklist',
			'categories' => [ 'content', 'data' ],
			'content' => '<bs:checklist />',
		], $result['droplets'][0] );
	}

	public function testTurnsEditorJsonIntoWikitext() {
		$result = $this->newExecution()->execute( $this->createMock( IMcpTool::class ), [] );
		$content = array_column( $result['droplets'], 'content', 'key' );
		$this->assertSame( '{{Textbox|boxtype=note|text=}}', $content['note'] );
		$this->assertSame( '{{#embed:|lang=}}', $content['embed'] );
		$this->assertArrayNotHasKey( 'layout', $content );
	}

	public function testFiltersByCategory() {
		$result = $this->newExecution()->execute(
			$this->createMock( IMcpTool::class ), [ 'category' => 'navigation' ]
		);
		$this->assertSame( [ 'subpages' ], array_column( $result['droplets'], 'key' ) );
	}

	public function testNotRegisteredWithoutWikiMCPToolsServices() {
		$services = $this->createMock( MediaWikiServices::class );
		$services->method( 'hasService' )->willReturn( false );
		$services->expects( $this->never() )->method( 'getService' );
		( new RegisterMCPTools() )->onMediaWikiServices( $services );
	}
}
