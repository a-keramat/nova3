<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new Nova\Core\Commands\RefreshRoutes);
Artisan::add(new Nova\Core\Commands\RefreshContent);
Artisan::add(new Nova\Core\Commands\Optimize($app['composer']));