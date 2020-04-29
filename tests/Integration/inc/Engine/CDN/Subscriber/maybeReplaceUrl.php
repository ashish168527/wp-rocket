<?php

namespace WP_Rocket\Tests\Integration\inc\Engine\CDN\Subscriber;

use WPMedia\PHPUnit\Integration\TestCase;

/**
 * @covers \WP_Rocket\Engine\CDN\Subscriber::maybe_replace_url
 * @uses   \WP_Rocket\Engine\CDN\CDN::get_cdn_urls
 * @group  CDN
 */
class Test_MaybeReplaceUrl extends TestCase {
	private $cnames;
	private $site_url;

	public function tearDown() {
		remove_filter( 'pre_get_rocket_option_cdn', [ $this, 'return_true' ] );
		remove_filter( 'rocket_cdn_cnames', [ $this, 'setCnames' ] );
		remove_filter( 'site_url', [ $this, 'setSiteURL' ] );

		parent::tearDown();
	}

	/**
	 * @dataProvider providerTestData
	 */
	public function testShouldMaybeReplaceURL( $original, $zones, $cdn_urls, $site_url, $expected ) {
		$this->cnames   = $cdn_urls;
		$this->site_url = $site_url;

		add_filter( 'pre_get_rocket_option_cdn', [ $this, 'return_true'] );
		add_filter( 'rocket_cdn_cnames', [ $this, 'setCnames' ] );
		add_filter( 'site_url', [ $this, 'setSiteURL' ] );

		$this->assertSame(
			$expected,
			apply_filters( 'rocket_asset_url', $original, $zones )
		);
	}

	public function providerTestData() {
		return $this->getTestData( __DIR__, 'maybe-replace-url' );
	}

	public function setCnames() {
		return $this->cnames;
	}

	public function setSiteURL() {
		return $this->site_url;
	}
}
