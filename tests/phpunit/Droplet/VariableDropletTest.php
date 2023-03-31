<?php

namespace MediaWiki\Extension\ContentDroplets\Tests\Droplet;

use MediaWiki\Extension\ContentDroplets\Droplet\VariableDroplet;
use PHPUnit\Framework\TestCase;

class VariableDropletTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\ContentDroplets\Droplet\BehaviourSwitchDroplet::getContent
	 * @dataProvider provideData
	 */
	public function testGetContent( $magicWord, $params, $expected ) {
		$mock = $this->getMockBuilder( VariableDroplet::class )
			->onlyMethods(
				[
					'getMagicWord', 'getParams', 'getName', 'getDescription',
					'getIcon', 'getRlModules', 'getCategories',
					'getVeCommand'
				]
			)->getMock();
		$mock->method( 'getMagicWord' )->willReturn( $magicWord );
		$mock->method( 'getParams' )->willReturn( $params );

		$this->assertSame( $expected, $mock->getContent() );
	}

	/**
	 * @return array[]
	 */
	public function provideData() {
		return [
			'no-params' => [
				'dummy',
				[],
				'{{DUMMY}}',
			],
			'with-params' => [
				'dummy',
				[ 'test', 'foo' ],
				'{{DUMMY:test|foo}}',
			],
		];
	}
}
