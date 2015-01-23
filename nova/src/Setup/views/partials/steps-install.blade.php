<?php

$path = Request::path();

$classes = [
	1 => '',
	2 => '',
	3 => '',
	4 => '',
	5 => '',
];

$dbActive = [
	'setup/install/config-database',
	'setup/install/config-database/check',
	'setup/install/config-database/write',
];

$emailActive = [
	'setup/install/config-email',
	'setup/install/config-email/write',
];

$novaActive = [
	'setup/install/nova',
];
$novaCompleted = [
	'setup/install/nova/success',
];

if (in_array($path, $dbActive))
{
	$classes[1] = ' class="step-active"';
}
if (File::exists(app('path.config').'/database.php'))
{
	$classes[1] = ' class="step-completed"';
}

if (in_array($path, $emailActive))
{
	$classes[2] = ' class="step-active"';
}
if (File::exists(app('path.config').'/mail.php'))
{
	$classes[2] = ' class="step-completed"';
}

if (in_array($path, $novaActive))
{
	$classes[3] = ' class="step-active"';
}
if (Cache::get('nova.installed'))
{
	$classes[3] = ' class="step-completed"';
}

?><ol>
	<li{!! $classes[1] !!}>{!! icon($_icons['1']) !!}Database Connection</li>
	<li{!! $classes[2] !!}>{!! icon($_icons['2']) !!}Email Settings</li>
	<li{!! $classes[3] !!}>{!! icon($_icons['3']) !!}Install {{ config('nova.app.name') }}</li>
	<li{!! $classes[4] !!}>{!! icon($_icons['4']) !!}Create User</li>
	<li{!! $classes[5] !!}>{!! icon($_icons['5']) !!}Finalize</li>
</ol>