<?php namespace Nova\Aegis;

use Illuminate\Support\Facades\Facade;

class AegisFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'nova.aegis'; }

}