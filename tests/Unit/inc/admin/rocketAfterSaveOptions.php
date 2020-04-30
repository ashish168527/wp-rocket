<?php

namespace WP_Rocket\Tests\Unit\inc\admin;

use Brain\Monkey\Functions;
use WP_Rocket\Tests\Unit\FilesystemTestCase;

/**
 * @covers ::rocket_after_save_options
 *
 * @group admin
 * @group Options
 * @group SaveOptions
 */
class Test_RocketAfterSaveOptions extends FilesystemTestCase {
	protected $path_to_test_data   = '/inc/admin/rocketAfterSaveOptions.php';

	public function setUp() {
		parent::setUp();

		// Mocks the various filesystem constants.
		$this->whenRocketGetConstant();

		require_once WP_ROCKET_PLUGIN_ROOT . 'inc/admin/options.php';

		Functions\when( 'wp_json_encode' )->alias(
			function ( $data, $options = 0, $depth = 512 ) {
				return json_encode( $data, $options, $depth );
			}
		);
	}

	protected function tearDown() {
		parent::tearDown();

		unset( $_POST['rocket_after_save_options'] );
	}

	/**
	 * @dataProvider providerTestData
	 */
	public function testShouldTriggerCleaningsWhenOptionsChange( $settings, $expected ) {
		if ( isset( $expected['rocket_clean_domain'] ) ) {
			Functions\expect( 'rocket_clean_domain' )->once()->andReturnNull();
			Functions\expect( 'home_url' )->andReturn( 'http://example.org' );
			Functions\expect( 'wp_remote_get' )
				->once()
				->with(
					'http://example.org',
					[
						'timeout'    => 0.01,
						'blocking'   => false,
						'user-agent' => 'WP Rocket/Homepage Preload',
						'sslverify'  => false,
					]
				)
				->andReturnNull();
		} else {
			Functions\expect( 'rocket_clean_domain' )->never();
		}

		if ( isset( $expected['rocket_clean_minify'] ) ) {
			Functions\expect( 'rocket_clean_minify' )->once()->with( 'js' )->andReturnNull();
		} else {
			Functions\expect( 'rocket_clean_minify' )->never();
		}

		if ( isset( $expected['rocket_generate_advanced_cache_file'] ) ) {
			$_POST['rocket_after_save_options'] = true;
			Functions\expect( 'rocket_generate_advanced_cache_file' )->once()->andReturnNull();
		} else {
			Functions\expect( 'rocket_generate_advanced_cache_file' )->never();
		}

		if ( isset( $expected['flush_rocket_htaccess'] ) ) {
			Functions\expect( 'rocket_valid_key' )->andReturn( true );
			Functions\expect( 'flush_rocket_htaccess' )->once()->with( false )->andReturnNull();
		} else {
			Functions\expect( 'flush_rocket_htaccess' )->never();
		}

		if ( isset( $expected['rocket_generate_config_file'] ) ) {
			Functions\expect( 'rocket_generate_config_file' )->once()->andReturnNull();
		} else {
			Functions\expect( 'rocket_generate_config_file' )->never();
		}

		if ( isset( $expected['set_rocket_wp_cache_define'] ) ) {
			$this->wp_cache = false;
			Functions\expect( 'set_rocket_wp_cache_define' )->once()->with( true )->andReturnNull();
		} else {
			$this->wp_cache = true;
			Functions\expect( 'set_rocket_wp_cache_define' )->never();
		}

		if ( isset( $expected['set_transient'] ) ) {
			Functions\expect( 'set_transient' )->with( 'rocket_analytics_optin', 1 )->andReturnNull();
		} else {
			Functions\expect( 'set_transient' )->with( 'rocket_analytics_optin', 1 )->never();
		}

		// Run it.
		rocket_after_save_options( $this->config['settings'], $settings );
	}
}