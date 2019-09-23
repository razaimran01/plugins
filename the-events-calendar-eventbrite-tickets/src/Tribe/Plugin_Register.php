<?php
/**
 * Class Tribe__Events__Tickets__Eventbrite__Plugin_Register
 */
class  Tribe__Events__Tickets__Eventbrite__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Events__Tickets__Eventbrite__Main';
	protected $dependencies = array(
		'parent-dependencies' => array(
			'Tribe__Events__Main'       => '4.8.1-dev',
		),
	);

	public function __construct() {
		$this->base_dir = EVENTBRITE_PLUGIN_FILE;
		$this->version  = Tribe__Events__Tickets__Eventbrite__Main::VERSION;

		$this->register_plugin();
	}
}