<?php

namespace MediaWiki\Extension\ContentDroplets\Tests\Droplet;

use MediaWiki\Extension\ContentDroplets\Droplet\TagDroplet;
use PHPUnit\Framework\TestCase;

class TagDropletTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\ContentDroplets\Droplet\TagDroplet::getContent
	 * @dataProvider provideData
	 */
	public function testGetContent( $target, $params, $hasContent, $expected ) {
		$mock = $this->getMockBuilder( TagDroplet::class )
			->onlyMethods(
				[
					'getTagName', 'getAttributes', 'getName', 'getDescription',
					'getIcon', 'getRlModules', 'getCategories', 'hasContent',
					'getVeCommand'
				]
			)->getMock();
		$mock->method( 'getTagName' )->willReturn( $target );
		$mock->method( 'getAttributes' )->willReturn( $params );
		$mock->method( 'hasContent' )->willReturn( $hasContent );

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
				false,
				'<Dummy />',
			],
			'params-has-content' => [
				'Dummy',
				[
					'foo' => 'bar',
					'dummy' => 'test'
				],
				true,
				"<Dummy foo=\"bar\" dummy=\"test\"></Dummy>",
			],
			'params-self-closing' => [
				'Dummy',
				[
					'foo' => 'bar',
					'dummy' => 'test'
				],
				false,
				"<Dummy foo=\"bar\" dummy=\"test\" />",
			]
		];
	}
}
