<?php namespace Nova\Core\Auth\Http\Controllers;

use Flash, BaseController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard,
	Illuminate\Contracts\Foundation\Application;

class AuthController extends BaseController {

	protected $auth;

	public function __construct(Application $app, Guard $auth)
	{
		parent::__construct($app);

		$this->auth = $auth;
		$this->structureView = 'auth';
		$this->templateView = 'auth';
	}

	public function getLogin()
	{
		$this->view = 'auth/login';
	}

	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required',
		]);

		// Grab the credentials out of the request
		$credentials = $request->only('email', 'password');

		// Remember the user?
		$remember = true;

		if ($this->auth->attempt($credentials, $remember))
		{
			return redirect()->intended(route('home'));
		}

		Flash::error("These credentials don't match our records.");

		return redirect()->back()->withInput($request->only('email'));
	}

	public function getLogout()
	{
		$this->auth->logout();

		return redirect()->route('home');
	}

}