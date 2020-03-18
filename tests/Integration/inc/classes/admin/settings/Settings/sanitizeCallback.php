<?php

namespace WP_Rocket\Tests\Integration\inc\classes\admin\settings\Settings;

use WPMedia\PHPUnit\Integration\AdminTestCase;

/**
 * @covers \WP_Rocket\Admin\Settings::sanitize_callback
 * @group  AdminOnly
 * @group  Settings
 */
class Test_SanitizeCallback extends AdminTestCase {
	/**
	 * @dataProvider addDataProvider
	 */
	public function testShouldSanitizeCriticalCss( $original, $sanitized ) {
		$actual = apply_filters( 'sanitize_option_wp_rocket_settings', $original );
		$this->assertSame(
			$sanitized['critical_css'],
			$actual['critical_css']
		);
	}

	public function addDataProvider() {
		return $this->getTestData( __DIR__, 'sanitizeCallback' );
	}
}
