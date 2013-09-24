<?php namespace Nova\Core\Events\Models;

use FormModel;
use FormDataModel;
use BaseModelEventHandler;

class User extends BaseModelEventHandler {

	public static $lang = 'user';
	public static $name = 'name';

	/**
	 * When a user is created, we need to create blank data records
	 * to prevent errors being thrown when the user is updated and
	 * we need to create the user preferences as well.
	 *
	 * @param	$model	The current model
	 * @return	void
	 */
	public function created($model)
	{
		/**
		 * Create the user settings.
		 */
		$model->createUserPreferences();
		
		/**
		 * Fill the user rows for the dynamic form with blank data for editing later.
		 */
		$form = FormModel::key('user')->first();
		
		if ($form->fields->count() > 0)
		{
			foreach ($form->fields as $field)
			{
				FormDataModel::create([
					'form_id' 	=> $form->id,
					'field_id' 	=> $field->id,
					'data_id' 	=> $model->id,
					'value' 	=> '',
				]);
			}
		}

		// Call the parent
		parent::created($model);
	}

}