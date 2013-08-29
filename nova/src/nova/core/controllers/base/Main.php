<?php namespace nova\core\controllers\base;

/**
 * All controllers in main sections of Nova (main, personnel, sim) extend from 
 * this base controller. This class is responsible for filling the template with 
 * the information that's often section specific, including setting up the 
 * navigation.
 *
 * DO NOT EDIT THIS FILE!
 *
 * @package		Nova
 * @subpackage	Core
 * @category	Controller
 * @author		Anodyne Productions
 * @copyright	2013 Anodyne Productions
 */

use Date;
use Nova;
use View;
use Sentry;
use Session;
use Location;
use SiteContent;
use SkinCatalog;
use BaseController;

abstract class Main extends BaseController {

	public function __construct()
	{
		parent::__construct();

		// Get a copy of the controller
		$me = $this;

		/**
		 * Before filter that populates some of the variables with data.
		 */
		$this->beforeFilter(function() use(&$me)
		{
			if ( ! $me->_stopExecution)
			{
				// Get the user
				$user = Sentry::getUser();

				// Set the variables
				$me->skin		= (Sentry::check()) ? $user->getPreferenceItem('skin_main') : $me->settings->skin_main;
				$me->rank		= (Sentry::check()) ? $user->getPreferenceItem('rank') : $me->settings->rank;
				$me->timezone	= (Sentry::check()) ? $user->getPreferenceItem('timezone') : $me->settings->timezone;
				$me->icons		= Nova::getIconIndex($me->skin);

				// Get the skin section info
				$me->_skinInfo	= SkinCatalog::getItems('location', $me->skin)->first();

				// Build the navigation
				$me->nav->setStyle($me->_skinInfo->nav)
					->setSection('main')
					->setCategory('main')
					->setType('main');

				if (Sentry::check())
				{
					// Has the user's role been updated since their last login?
					$lastLogin = $user->last_login->diffInMinutes($user->role->updated_at, false);
					$lastUpdate = $user->updated_at->diffInMinutes($user->role->updated_at, false);

					if ($lastLogin > 0 and $lastUpdate > 0)
					{
						# TODO: remove this once we've verified it's working right
						\Log::info("Session updated (Last Login: {$lastLogin}) (Last Update: {$lastUpdate})");
						
						// Clear the access info from the session
						Session::forget('role');

						// Update the access info in the session
						$user->getPermissions();
					}
				}
			}
		});
	}

	/**
	 * Setup the layout.
	 *
	 * @return	void
	 */
	protected function setupLayout()
	{
		if ( ! $this->_stopExecution)
		{
			// Set the values to be passed to the structure
			$vars = array(
				'skin'		=> $this->skin,
				'skinInfo'	=> $this->_skinInfo,
				'section'	=> 'main',
				'settings'	=> $this->settings,
			);

			// Setup the layout and its data
			$layout				= View::make(Location::structure('main'))->with($vars);
			$layout->title		= $this->settings->sim_name.' &bull; ';
			$layout->javascript	= false;
			
			// Setup the template and its data
			$layout->template			= View::make(Location::template('main'))->with($vars);
			$layout->template->ajax		= false;
			$layout->template->flash	= false;
			$layout->template->content	= false;
			$layout->template->header	= false;
			$layout->template->message	= false;
			$layout->template->navmain	= $this->nav->build();
			
			// Setup the subnav and widgets
			$layout->template->navsub			= View::make(Location::partial('navsub'));
			$layout->template->navsub->menu		= false;
			$layout->template->navsub->widget1	= false;
			$layout->template->navsub->widget2	= false;
			$layout->template->navsub->widget3	= false;

			// Setup the footer
			$layout->template->footer			= View::make(Location::partial('footer'));
			$layout->template->footer->extra	= SiteContent::getContentItem('other.footer');

			// Pass everything back to the layout
			$this->layout = $layout;
		}
	}

}