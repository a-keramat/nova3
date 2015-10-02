<?php namespace Nova\Foundation\Providers;

use Permission;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [];

	/**
	 * Register any application authentication / authorization services.
	 *
	 * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
	 * @return void
	 */
	public function boot(GateContract $gate)
	{
		// Build the list of policies
		$this->buildPolicyList();

		// Register the policies with the framework
		parent::registerPolicies($gate);

		if (app('nova.setup')->isInstalled())
		{
			// Grab all of the permissions, loop through them, and define the abilities
			foreach ($this->getPermissions() as $permission)
			{
				$gate->define($permission->name, function($user) use ($permission)
				{
					return $user->hasRole($permission->roles);
				});
			}
		}
	}

	protected function buildPolicyList()
	{
		$items = ['Menu', 'MenuItem', 'Page', 'PageContent'];

		foreach ($items as $item)
		{
			$model = "{$item}";
			$policy = "{$item}Policy";

			$this->policies[alias($model)] = alias($policy);
		}
	}

	protected function getPermissions()
	{
		return app('PermissionRepository')->all(['roles']);
	}
	
}