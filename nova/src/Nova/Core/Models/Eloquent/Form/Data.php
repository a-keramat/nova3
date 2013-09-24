<?php namespace Nova\Core\Models\Eloquent\Form;

use Model;
use FormTrait;

class Data extends Model {

	use FormTrait;
	
	protected $table = 'form_data';

	protected $fillable = array(
		'form_id', 'field_id', 'data_id', 'value', 'created_by',
	);

	protected $dates = array('created_at', 'updated_at');
	
	protected static $properties = array(
		'id', 'form_id', 'field_id', 'data_id', 'value', 'created_by',
		'created_at', 'updated_at',
	);

	/*
	|--------------------------------------------------------------------------
	| Relationships
	|--------------------------------------------------------------------------
	*/

	/**
	 * Belongs To: Form
	 */
	public function form()
	{
		return $this->belongsTo('FormModel', 'form_id');
	}

	/**
	 * Belongs To: Field
	 */
	public function field()
	{
		return $this->belongsTo('FormFieldModel', 'field_id');
	}

	/**
	 * Belongs To: User
	 */
	public function author()
	{
		return $this->belongsTo('UserModel', 'created_by');
	}

	/*
	|--------------------------------------------------------------------------
	| Model Scopes
	|--------------------------------------------------------------------------
	*/

	/**
	 * Scope the query to form data by entry ID.
	 *
	 * @param	Builder		The query builder
	 * @param	int			Entry ID
	 * @return	void
	 */
	public function scopeEntry($query, $id)
	{
		$query->where('data_id', $id);
	}

	/**
	 * Scope the query to form data by form field.
	 *
	 * @param	Builder		The query builder
	 * @param	int			Field ID
	 * @return	void
	 */
	public function scopeFormField($query, $id)
	{
		$query->where('field_id', $id);
	}

	/*
	|--------------------------------------------------------------------------
	| Model Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * Update data in the data table.
	 *
	 * @param	int		The ID to udpate
	 * @param	array 	An array of data to use in the update
	 * @return	bool
	 */
	public static function updateData($formKey, $id, array $data)
	{
		$results = [];

		// Start a new query
		$query = static::startQuery();

		// Get all the records for this entry
		$entries = $query->key($formKey)->entry($id)->get();

		foreach ($entries as $entry)
		{
			if (array_key_exists($entry->field_id, $data))
			{
				$retval = $entry->update(['value' => trim(e($data[$entry->field_id]))]);

				$results[] = $retval;
			}
		}

		if (in_array(false, $results))
		{
			return false;
		}

		return true;
	}

}