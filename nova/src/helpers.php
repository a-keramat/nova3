<?php

if (! function_exists('_m')) {
	function _m($key, $args = [])
	{
		//$gender = (user()) ? user()->gender : 'male';
		$gender = 'male';

		return app('nova.translator')->msg($key, [
			'parsemag' => true,
			'variables' => array_merge([$gender], $args)
		]);
	}
}

if (! function_exists('d')) {
	function d()
	{
		array_map(function ($x) {
			(new Illuminate\Support\Debug\Dumper)->dump($x);
		}, func_get_args());
	}
}