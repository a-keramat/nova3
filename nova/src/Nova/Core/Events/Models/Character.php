<?php namespace Nova\Core\Events\Models;

use FormModel;
use SystemEvent;
use FormDataModel;
use BaseModelEventHandler;

class Character extends BaseModelEventHandler {

	public static $lang = 'character';
	public static $name = 'last_name';

	/**
	 * After create event
	 *
	 * When a character is created, we need to create blank data records
	 * to prevent errors being thrown when the character is updated.
	 *
	 * @param	$model	The current model
	 * @return	void
	 */
	public function created($model)
	{
		/**
		 * Fill the character rows for the dynamic form with blank data for editing later.
		 */
		$form = FormModel::key('character')->first();
		
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

		/**
		 * System Event
		 */
		SystemEvent::addUserEvent('event.item', lang('character'), $model->getName(), lang('action.created'));
	}

}