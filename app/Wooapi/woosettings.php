<?php

use Automattic\WooCommerce\Client;

return new Client(
	'https://alla.ph',
	'ck_b7144d17091aa01a7a096154a445180c603dfc61',
	'cs_cdb7705d4ad5bf29aa2b6366c55ac98397e4ff07',
	[
		'wp_api'  => true,
		'version' => 'wc/v2',
	]
);