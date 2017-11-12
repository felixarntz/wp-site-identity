<?php

class Tests_WPSI extends WPSI_UnitTestCase {

	public function test_wpsi() {
		$this->assertInstanceOf( 'WP_Site_Identity', wpsi() );
	}
}
