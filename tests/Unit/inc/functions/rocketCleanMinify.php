<?php

namespace WP_Rocket\Tests\Unit\inc\functions;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use WP_Rocket\Tests\Unit\FilesystemTestCase;

/**
 * @covers ::rocket_clean_minify
 * @uses  ::rocket_direct_filesystem
 *
 * @group Functions
 * @group Files
 * @group vfs
 */
class Test_RocketCleanMinify extends FilesystemTestCase {
	protected $path_to_test_data = '/inc/functions/rocketCleanMinify.php';
	private   $valid_extensions  = [ 'css', 'css.gz', 'js', 'js.gz' ];

	public function setUp() {
		parent::setUp();

		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\expect( 'rocket_get_constant' )->with( 'WP_ROCKET_MINIFY_CACHE_PATH' )->andReturn( 'vfs://public/wp-content/cache/min/' );
	}

	/**
	 * @dataProvider providerTestData
	 */
	public function testShouldCleanMinified( $extensions, $expected ) {
		$this->getShouldNotCleanEntries( $expected['non_cleaned'] );

		foreach ( (array) $extensions as $ext ) {
			if ( ! in_array( $ext, $this->valid_extensions, true ) ) {
				continue;
			}
			Actions\expectDone( 'before_rocket_clean_minify' )->once()->with( $ext );
			Actions\expectDone( 'after_rocket_clean_minify' )->once()->with( $ext );
		}

		rocket_clean_minify( $extensions );

		$this->checkCleanedIsDeleted( $expected['cleaned'] );
		$this->checkNonCleanedExist( isset( $expected['dump_results'] ) );
	}
}
