<?php namespace Nova\Core\Controllers\Admin;

use Nova;
use View;
use Input;
use Location;
use Redirect;
use DynamicForm;
use FormValidator;
use FormTabValidator;
use FormFieldValidator;
use FormValueValidator;
use AdminBaseController;
use FormSectionValidator;
use FormRepositoryInterface;

class Form extends AdminBaseController {

	public function __construct(FormRepositoryInterface $form)
	{
		parent::__construct();

		// Set the injected interfaces
		$this->form = $form;
	}

	public function getIndex($formKey = false)
	{
		// Verify the user is allowed
		$this->currentUser->allowed(['form.create', 'form.update', 'form.delete'], true);

		// Set the JS view
		$this->jsView = 'admin/form/forms_js';

		if ($formKey !== false)
		{
			// Set the view
			$this->view = 'admin/form/forms_action';

			if ($formKey !== "0")
			{
				// Get the form
				$form = $this->data->form = $this->form->findByKey($formKey);

				// Get the form fields
				$this->data->formFields[0] = lang('short.selectOne', langConcat('form field'));
				$this->data->formFields+= $form->fields()
					->active()
					->orderAsc('label')
					->get()
					->toSimpleArray('id', 'label');
			}
			else
			{
				$this->data->form = false;
			}

			// Set the action
			$this->mode = $this->data->action = ($formKey === '0') ? 'create' : 'update';
		}
		else
		{
			// Set the view
			$this->view = 'admin/form/forms';

			// Get all the forms
			$form = $this->data->forms = $this->form->all();

			// Build the delete form modal
			$this->ajax[] = View::make(Location::partial('common/modal'))
				->with('modalId', 'deleteForm')
				->with('modalHeader', lang('Short.delete', lang('Form')))
				->with('modalBody', false)
				->with('modalFooter', false);
		}
	}
	public function postIndex($formKey = false)
	{
		// Get the action
		$formAction = e(Input::get('formAction'));

		// Set up the validation service
		$validator = new FormValidator;

		// If the validation fails, stop and go back
		if ( ! $validator->passes())
		{
			// Set the flash message
			$flashMessage = lang('Short.validate', lang('action.failed')).'. ';
			$flashMessage.= implode(' ', $validator->getErrors()->all());

			return Redirect::to('admin/form')
				->with('flashStatus', 'danger')
				->with('flashMessage', $flashMessage);
		}

		/**
		 * Create the form.
		 */
		if ($this->currentUser->hasAccess('form.create') and $formAction == 'create')
		{
			// Create the form
			$item = $this->form->create(Input::all());

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.create', lang('form'))
				: lang('Short.alert.failure.create', lang('form'));
		}

		/**
		 * Update the form.
		 */
		if ($this->currentUser->hasAccess('form.update') and $formAction == 'update')
		{
			// Update the form
			$item = $this->form->update(Input::get('id'), Input::all());

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.update', lang('form'))
				: lang('Short.alert.failure.update', lang('form'));
		}

		/**
		 * Delete the form.
		 */
		if ($this->currentUser->hasAccess('form.delete') and $formAction == 'delete')
		{
			// Delete the form
			try
			{
				$item = $this->form->delete(Input::get('id'));

				// Set the flash info
				$flashStatus = ($item) ? 'success' : 'danger';
				$flashMessage = ($item)
					? lang('Short.alert.success.delete', lang('form'))
					: lang('Short.alert.failure.delete', lang('form'));
			}
			catch (\FormProtectedException $e)
			{
				// Form is protected
				$flashStatus = 'danger';
				$flashMessage = lang('error.admin.protectedForm');
			}
		}

		return Redirect::to('admin/form')
			->with('flashStatus', $flashStatus)
			->with('flashMessage', $flashMessage);
	}

	public function getTabs($formKey, $id = false)
	{
		// Verify the user is allowed
		$this->currentUser->allowed(['form.create', 'form.update', 'form.delete'], true);

		// Set the view files
		$this->view = 'admin/form/tabs';
		$this->jsView = 'admin/form/tabs_js';

		// Pass along the form key to the view
		$this->data->formKey = $formKey;

		// Get the form and pass it along to the view
		$form = $this->data->form = $this->form->findByKey($formKey);

		// Get the tabs
		$tabs = $form->tabs;

		// If there isn't an ID, show all the tabs
		if ($id === false)
		{
			// Set up the variables
			$this->data->tabs = false;

			if ($tabs->count() > 0)
			{
				// Sort the tabs
				$tabs = $tabs->sortBy(function($t)
				{
					return $t->order;
				});

				foreach ($tabs as $tab)
				{
					$this->data->tabs[] = $tab;
				}
			}
		}
		else
		{
			// Set the view
			$this->view = 'admin/form/tabs_action';

			// Get the tab
			$tab = $this->data->tab = $form->tabs()->find($id);

			// Clear out the message for this page
			$this->data->message = false;

			// ID 0 means a new tab, anything else edits an existing tab
			if ((int) $id === 0)
			{
				// Set the action
				$this->mode = $this->data->action = 'create';
			}
			else
			{
				// Set the action
				$this->mode = $this->data->action = 'update';

				// If we don't have a tab, redirect to the creation screen
				if ($this->data->tab === null)
				{
					Redirect::to("admin/form/tabs/{$formKey}/0");
				}

				// If the tab isn't part of this form, redirect them
				if ($tab->form->key != $formKey)
				{
					Redirect::to("admin/form/tabs/{$tab->form->key}/{$id}");
				}
			}
		}

		// Build the delete tab modal
		$this->ajax[] = View::make(Location::partial('common/modal'))
			->with('modalId', 'deleteTab')
			->with('modalHeader', lang('Short.delete', langConcat('Form Tab')))
			->with('modalBody', '')
			->with('modalFooter', false);
	}
	public function postTabs($formKey)
	{
		// Get the action
		$formAction = e(Input::get('formAction'));

		// Set up the validation service
		$validator = new FormTabValidator;

		// If the validation fails, stop and go back
		if ( ! $validator->passes())
		{
			if ($formAction == 'delete')
			{
				// Set the flash message
				$flashMessage = lang('Short.validate', lang('action.failed')).'. ';
				$flashMessage.= implode(' ', $validator->getErrors()->all());

				return Redirect::to('admin/form/tabs')
					->with('flashStatus', 'danger')
					->with('flashMessage', $flashMessage);
			}
			
			return Redirect::back()->withInput()->withErrors($validator->getErrors());
		}

		// Get the form
		$form = $this->form->findByKey($formKey);

		/**
		 * Create a form tab.
		 */
		if ($this->currentUser->hasAccess('form.create') and $formAction == 'create')
		{
			// Create the form tab
			$item = $this->form->createTab(Input::all(), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.create', langConcat('form tab'))
				: lang('Short.alert.failure.create', langConcat('form tab'));
		}

		/**
		 * Edit a form tab.
		 */
		if ($this->currentUser->hasAccess('form.update') and $formAction == 'update')
		{
			// Update the tab
			$item = $this->form->updateTab(Input::get('id'), Input::all(), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.update', langConcat('form tab'))
				: lang('Short.alert.failure.update', langConcat('form tab'));
		}

		/**
		 * Delete the form tab.
		 */
		if ($this->currentUser->hasAccess('form.delete') and $formAction == 'delete')
		{
			// Delete the tab
			$item = $this->form->deleteTab(Input::get('id'), Input::get('new_tab_id'));

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.delete', langConcat('form tab'))
				: lang('Short.alert.failure.delete', langConcat('form tab'));
		}

		return Redirect::to("admin/form/tabs/{$formKey}")
			->with('flashStatus', $flashStatus)
			->with('flashMessage', $flashMessage);
	}

	public function getSections($formKey, $id = false)
	{
		// Verify the user is allowed
		$this->currentUser->allowed(['form.create', 'form.update', 'form.delete'], true);

		// Set the view files
		$this->view = 'admin/form/sections';
		$this->jsView = 'admin/form/sections_js';

		// Pass along the form key to the view
		$this->data->formKey = $formKey;

		// Get the form and pass it along to the view
		$form = $this->data->form = $this->form->findByKey($formKey);

		// If there isn't an ID, show all the sections
		if ($id === false)
		{
			// Get the form sections
			$sections = $form->sections;

			// Set up the variables
			$this->data->tabs = false;
			$this->data->sections = false;

			// If we have tabs, get them
			if ($form->tabs->count() > 0)
			{
				$this->data->tabs = $form->tabs->sortBy(function($t)
				{
					return $t->order;
				});
			}

			// If we have sections, get them
			if ($sections->count() > 0)
			{
				// Sort the sections
				$sections = $sections->sortBy(function($s)
				{
					return $s->order;
				});

				foreach ($sections as $section)
				{
					if ($section->tab_id > 0)
					{
						$this->data->sections[$section->tab_id][] = $section;
					}
					else
					{
						$this->data->sections[] = $section;
					}
				}
			}
		}
		else
		{
			// Set the view
			$this->view = 'admin/form/sections_action';

			// Get all the tabs
			$tabs = $this->data->tabs = $form->tabs->toSimpleArray('id', 'name');

			// Get the section
			$section = $this->data->section = $form->sections()->find($id);

			// Clear out the message for this page
			$this->data->message = false;

			// ID 0 means a new section, anything else edits an existing section
			if ((int) $id === 0)
			{
				// Set the action
				$this->mode = $this->data->action = 'create';
			}
			else
			{
				// Set the action
				$this->mode = $this->data->action = 'update';

				// If we don't have a section, redirect to the creation screen
				if ($this->data->section === null)
				{
					Redirect::to("admin/form/sections/{$formKey}/0");
				}

				// If the section isn't part of this form, redirect them
				if ($this->data->section->form->key != $formKey)
				{
					Redirect::to("admin/form/sections/{$section->form->key}/{$id}");
				}
			}
		}

		// Build the delete section modal
		$this->ajax[] = View::make(Location::partial('common/modal'))
			->with('modalId', 'deleteSection')
			->with('modalHeader', lang('Short.delete', langConcat('Form Section')))
			->with('modalBody', '')
			->with('modalFooter', false);
	}
	public function postSections($formKey)
	{
		// Get the action
		$formAction = e(Input::get('formAction'));

		// Set up the validation service
		$validator = new FormSectionValidator;

		// If the validation fails, stop and go back
		if ( ! $validator->passes())
		{
			if ($formAction == 'delete')
			{
				// Set the flash message
				$flashMessage = lang('Short.validate', lang('action.failed')).'. ';
				$flashMessage.= implode(' ', $validator->getErrors()->all());

				return Redirect::to('admin/form/sections')
					->with('flashStatus', 'danger')
					->with('flashMessage', $flashMessage);
			}
			
			return Redirect::back()->withInput()->withErrors($validator->getErrors());
		}

		// Get the form
		$form = $this->form->findByKey($formKey);

		/**
		 * Create a form section.
		 */
		if ($this->currentUser->hasAccess('form.create') and $formAction == 'create')
		{
			// Create the section
			$item = $this->form->createSection(Input::all(), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.create', langConcat('form section'))
				: lang('Short.alert.failure.create', langConcat('form section'));
		}

		/**
		 * Edit a form section.
		 */
		if ($this->currentUser->hasAccess('form.update') and $formAction == 'update')
		{
			// Update the section
			$item = $this->form->updateSection(Input::get('id'), Input::all(), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.update', langConcat('form section'))
				: lang('Short.alert.failure.update', langConcat('form section'));
		}

		/**
		 * Delete the form section.
		 */
		if ($this->currentUser->hasAccess('form.delete') and $formAction == 'delete')
		{
			// Delete the section
			$item = $this->form->deleteSection(Input::get('id'), Input::get('new_section_id'), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.delete', langConcat('form section'))
				: lang('Short.alert.failure.delete', langConcat('form section'));
		}

		return Redirect::to("admin/form/sections/{$formKey}")
			->with('flashStatus', $flashStatus)
			->with('flashMessage', $flashMessage);
	}

	public function getFields($formKey, $id = false)
	{
		// Verify the user is allowed
		$this->currentUser->allowed(['form.create', 'form.update', 'form.delete'], true);

		// Set the view files
		$this->view = 'admin/form/fields';
		$this->jsView = 'admin/form/fields_js';

		// Pass along the form key to the view
		$this->data->formKey = $formKey;

		// Get the form and pass it along
		$form = $this->data->form = $this->form->findByKey($formKey);

		// If there isn't an ID, show all the fields
		if ($id === false)
		{
			// Setup the dynamic form and assemble the elements
			$formOutput = DynamicForm::setup($formKey, false, true);
			$formOutput->assemble();

			// Set up the variables
			$this->data->tabs = $formOutput->getData('tabs');
			$this->data->sections = $formOutput->getData('sections');
			$this->data->fields = $formOutput->getData('fields');

			// Build the delete field modal
			$this->ajax[] = View::make(Location::partial('common/modal'))
				->with('modalId', 'deleteField')
				->with('modalHeader', lang('Short.delete', langConcat('Form Field')))
				->with('modalBody', '')
				->with('modalFooter', false);
		}
		else
		{
			// Set the view
			$this->view = 'admin/form/fields_action';

			// Get the field
			$field = $this->data->field = $form->fields()->find($id);

			// Set the field types
			$this->data->types = [
				'text'		=> lang('Text_field'),
				'textarea'	=> lang('Text_area'),
				'select'	=> lang('Dropdown'),
			];

			// Resolve the binding
			$role = Nova::resolveBinding('AccessRoleRepositoryInterface');

			// Set the access restrictions
			$this->data->accessRoles = $role->all();

			// Get the tabs and sections
			$this->data->tabs[0] = lang('short.selectOne', lang('tab'));
			$this->data->tabs+= $form->tabs->toSimpleArray('id', 'name');
			$this->data->sections[0] = lang('short.selectOne', lang('section'));
			$this->data->sections+= $form->sections->toSimpleArray('id', 'name');

			// Clear out the message for this page
			$this->data->message = false;

			// ID 0 means a new section, anything else edits an existing section
			if ((int) $id === 0)
			{
				// Set the action
				$this->mode = $this->data->action = 'create';
			}
			else
			{
				// Set the action
				$this->mode = $this->data->action = 'update';

				// Get the field values
				$this->data->values = $field->values->sortBy(function($v)
				{
					return $v->order;
				});

				// If we don't have a field, redirect to the creation screen
				if ($field === null)
				{
					Redirect::to("admin/form/fields/{$formKey}/0");
				}

				// If the field isn't part of this form, redirect them
				if ($field->form->key != $formKey)
				{
					Redirect::to("admin/form/fields/{$field->form->key}/{$id}");
				}
			}
		}
	}
	public function postFields($formKey)
	{
		// Get the action
		$formAction = e(Input::get('formAction'));

		// Set up the validation service
		$validator = new FormFieldValidator;

		// If the validation fails, stop and go back
		if ( ! $validator->passes())
		{
			if ($formAction == 'delete')
			{
				// Set the flash message
				$flashMessage = lang('Short.validate', lang('action.failed')).'. ';
				$flashMessage.= implode(' ', $validator->getErrors()->all());

				return Redirect::to("admin/form/fields/{$formKey}")
					->with('flashStatus', 'danger')
					->with('flashMessage', $flashMessage);
			}
			
			return Redirect::back()->withInput()->withErrors($validator->getErrors());
		}

		// Get the form
		$form = $this->form->findByKey($formKey);

		/**
		 * Create a form field.
		 */
		if ($this->currentUser->hasAccess('form.create') and $formAction == 'create')
		{
			// Create the field
			$item = $this->form->createField(Input::all(), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.create', langConcat('form field'))
				: lang('Short.alert.failure.create', langConcat('form field'));
		}

		/**
		 * Edit a form field.
		 */
		if ($this->currentUser->hasAccess('form.update') and $formAction == 'update')
		{
			// Update the field
			$item = $this->form->updateField(Input::get('id'), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.update', langConcat('form field'))
				: lang('Short.alert.failure.update', langConcat('form field'));
		}

		/**
		 * Delete a form field.
		 */
		if ($this->currentUser->hasAccess('form.delete') and $formAction == 'delete')
		{
			// Delete the field
			$item = $this->form->deleteField(Input::get('id'), $form);

			// Set the flash info
			$flashStatus = ($item) ? 'success' : 'danger';
			$flashMessage = ($item) 
				? lang('Short.alert.success.delete', langConcat('form field'))
				: lang('Short.alert.failure.delete', langConcat('form field'));
		}

		return Redirect::to("admin/form/fields/{$formKey}")
			->with('flashStatus', $flashStatus)
			->with('flashMessage', $flashMessage);
	}

}