<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	//app_path().'/commands',
	//app_path().'/controllers',
	//app_path().'/models',
	//app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Errors
|--------------------------------------------------------------------------
|
| Here we will configure the 404 page that'll be thrown whenever a page
| can't be found. The view file can be overridden with seamless substitution
| if the admin so chooses.
|
*/

App::missing(function()
{
	// Random headers and messages to use
	$messages = array(
		0 => array(
			'header' => "Aw, crap!",
			'message' => "Looks like what you're trying to find isn't here. It was probably moved, or sucked into a black hole. Chin up."),
		1 => array(
			'header' => "Bloody Hell!",
			'message' => "The rabbits have been nibbling on the cables again."),
		2 => array(
			'header' => "Uh Oh!",
			'message' => "You seem to have stumbled off the beaten path. Perhaps you should try again."),
		3 => array(
			'header' => "Nope, not here.",
			'message' => "That file ain't there. Kind of pathetic really."),
		4 => array(
			'header' => "Danger, Will Robinson! Danger!",
			'message' => "We couldn't find what you were looking for. Try again."),
		5 => array(
			'header' => "Doh!",
			'message' => "We lost that page. Try again."),
		6 => array(
			'header' => "What have you done?!",
			'message' => "I take my eye off you for one minute and this is where I find you?!"),
		7 => array(
			'header' => "Congratulations, you broke the Internet",
			'message' => "The page you're after doesn't exist. Try again."),
		8 => array(
			'header' => "404'd",
			'message' => "Boy, you sure are stupid.\r\nWere you just making up filenames or what? I mean, I've seen some pretend file names in my day, but come on! It's like you're not even trying."),
		9 => array(
			'header' => "Error 404: Page Not Found",
			'message' => "We actually know where the page is. Chuck Norris has it and he decided to keep it."),
		10 => array(
			'header' => "404 Error",
			'message' => "This is not the page you're looking for.\r\nMove along. Move along."),
		11 => array(
			'header' => "202 + 202 = 404",
			'message' => "For those who aren't great at math, that means your page couldn't be found. Try again."),
		12 => array(
			'header' => "Bummer!",
			'message' => "aka Error 404\r\nThe web address you entered is not a functioning page on the site."),
		13 => array(
			'header' => "Page Not Found",
			'message' => "We think it may have been murdered.\r\nProfessor Plum, in the Ball Room, with the Wrench."),
		14 => array(
			'header' => "This is awkward...",
			'message' => "Sooooo, we kind of, sort of, can't find that page. Try another one?"),
	);
	
	// Get a random item
	$rand = array_rand($messages);

	// Log the error
	Log::error('404 Not Found. Could not find the page requested: '.Request::path());

	return View::make(Location::error('404'))
		->with('header', $messages[$rand]['header'])
		->with('message', $messages[$rand]['message']);
});

/**
 * Runtime exception
 */
App::error(function(RuntimeException $ex, $code)
{
	switch ($ex->getMessage())
	{
		case 'Nova 3 requires PHP 5.4.0 or higher':
			return View::make(Location::error('php_version'))
				->with('env', App::make('env'));
		break;
	}
});

/**
 * General exception
 */
App::error(function(Exception $ex, $code)
{
	Log::error($ex);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});