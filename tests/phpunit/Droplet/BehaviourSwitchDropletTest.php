<?php

namespace MediaWiki\Extension\ContentDroplets\Tests\Droplet;

use MediaWiki\Extension\ContentDroplets\Droplet\BehaviourSwitchDroplet;
use PHPUnit\Framework\TestCase;

class BehaviourSwitchDropletTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\ContentDroplets\Droplet\BehaviourSwitchDroplet::getContent
	 * @dataProvider provideData
	 */
	public function testGetContent( $magicWord, $expected ) {
		$mock = $this->getMockBuilder( BehaviourSwitchDroplet::class )
			->onlyMethods(
				[
					'getMagicWord', 'getName', 'getDescription',
					'getIcon', 'getRlModules', 'getCategories',
					'getVeCommand'
				]
			)->getMock();
		$mock->method( 'getMagicWord' )->willReturn( $magicWord );

		$this->assertSame( $expected, $mock->getContent() );
	}

	/**
	 * @return array[]
	 */
	public static function provideData() {
		return [
			'lowercase' => [
				'dummy',
				'__DUMMY__',
			],
			'mixed' => [
				'DuMMy',
				'__DUMMY__',
			],
		];
	}
}
