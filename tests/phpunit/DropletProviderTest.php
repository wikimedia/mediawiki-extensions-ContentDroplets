<?php

namespace MediaWiki\Extension\ContentDroplets\Tests;

use MediaWiki\Extension\ContentDroplets\DropletProvider;
use MediaWiki\Extension\ContentDroplets\IDropletDescription;
use MediaWiki\Extension\ContentDroplets\IDropletSource;
use MediaWiki\Language\RawMessage;
use PHPUnit\Framework\TestCase;

class DropletProviderTest extends TestCase {
	/**
	 * @covers \MediaWiki\Extension\ContentDroplets\DropletProvider::getDroplets
	 */
	public function testGetDroplets() {
		$sources = $this->getSources();

		$provider = new DropletProvider( $sources );
		$droplets = $provider->getDroplets();

		$this->assertSame( [
			'foo', 'dummy', 'droplet1', 'droplet2'
		], array_keys( $droplets ) );

		$this->assertSame( 'Foo', $droplets['dummy']->getName()->text() );
	}

	/**
	 * @return array
	 */
	private function getSources() {
		$droplet1Mock = $this->createMock( IDropletDescription::class );
		$droplet1Mock->method( 'getName' )->willReturn( new RawMessage( 'Dummy' ) );
		$source1Mock = $this->createMock( IDropletSource::class );
		$source1Mock->method( 'getDroplets' )->willReturn( [
			'foo' => $this->createMock( IDropletDescription::class ),
			'dummy' => $droplet1Mock
		] );

		// Droplet with the same name as in previous source,
		// but different properties => test overriding
		$droplet2Mock = $this->createMock( IDropletDescription::class );
		$droplet2Mock->method( 'getName' )->willReturn( new RawMessage( 'Foo' ) );
		$source2Mock = $this->createMock( IDropletSource::class );
		$source2Mock->method( 'getDroplets' )->willReturn( [
			'dummy' => $droplet2Mock,
			'droplet1' => $this->createMock( IDropletDescription::class ),
			'droplet2' => $this->createMock( IDropletDescription::class ),
		] );

		return [
			's1' => $source1Mock,
			's2' => $source2Mock
		];
	}
}
