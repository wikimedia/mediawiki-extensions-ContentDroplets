<?php

namespace MediaWiki\Extension\ContentDroplets\Tests\Droplet;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use PHPUnit\Framework\TestCase;

class TemplateDropletTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet::getContent
	 * @dataProvider provideData
	 */
	public function testGetContent( $target, $params, $expected ) {
		$mock = $this->getMockBuilder( TemplateDroplet::class )
			->onlyMethods(
				[
					'getTarget', 'getParams', 'getName', 'getDescription',
					'getIcon', 'getRlModules', 'getCategories',
					'getVeCommand'
				]
			)
			->getMock();
		$mock->method( 'getTarget' )->willReturn( $target );
		$mock->method( 'getParams' )->willReturn( $params );

		$this->assertSame( $expected, $mock->getContent() );
	}

	/**
	 * @return array[]
	 */
	public static function provideData() {
		return [
			'no-params' => [
				'Dummy',
				[],
				'{"target":{"href":"./Template:Dummy","wt":"Dummy"},"params":[]}'
			],
			'named-params' => [
				'Dummy',
				[
					'foo' => 'bar',
					'dummy' => 'test'
				],
				'{"target":{"href":"./Template:Dummy","wt":"Dummy"},"params":{"foo":"bar","dummy":"test"}}'
			],
			'unnamed-params' => [
				'Dummy',
				[
					'foo', 'bar'
				],
				'{"target":{"href":"./Template:Dummy","wt":"Dummy"},"params":{"1":"foo","2":"bar"}}'
			]
		];
	}
}
