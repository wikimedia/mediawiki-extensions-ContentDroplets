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
		$source1Mock = $this->createMock( IDropletSource::class );
		$source1Mock->method( 'getDroplets' )->willReturn( [
			'foo' => $this->createDropletMock( null, true ),
			'dummy' => $this->createDropletMock( 'Dummy', true ),
			'hidden' => $this->createDropletMock( null, false ),
		] );

		// Droplet with the same name as in previous source,
		// but different properties => test overriding
		$source2Mock = $this->createMock( IDropletSource::class );
		$source2Mock->method( 'getDroplets' )->willReturn( [
			'dummy' => $this->createDropletMock( 'Foo', true ),
			'droplet1' => $this->createDropletMock( null, true ),
			'droplet2' => $this->createDropletMock( null, true ),
			'hidden' => $this->createDropletMock( null, false ),
		] );

		return [
			's1' => $source1Mock,
			's2' => $source2Mock
		];
	}

	private function createDropletMock( ?string $name = null, ?bool $visible = null ): IDropletDescription {
		$dropletMock = $this->createMock( IDropletDescription::class );
		if ( $name !== null ) {
			$dropletMock->method( 'getName' )->willReturn( new RawMessage( $name ) );
		}
		if ( $visible !== null ) {
			$dropletMock->method( 'listDroplet' )->willReturn( $visible );
		}
		return $dropletMock;
	}
}
