<?php namespace nova\core\models\entities\form;

use Event;
use Model;
use Config;
use FormTrait;

class Tab extends Model {

	use FormTrait;
	
	protected $table = 'form_tabs';

	protected $fillable = array(
		'form_id', 'name', 'link_id', 'order', 'status',
	);

	protected $dates = array('created_at', 'updated_at');
	
	protected static $properties = array(
		'id', 'form_id', 'name', 'link_id', 'order', 'status', 'created_at', 
		'updated_at',
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
		return $this->belongsTo('NovaForm', 'form_id');
	}

	/**
	 * Has Many: Sections
	 */
	public function sections()
	{
		return $this->hasMany('NovaFormSection')->orderAsc('order');
	}

	/**
	 * Has Many: Fields
	 */
	public function fields()
	{
		return $this->hasMany('NovaFormField')->orderAsc('order');
	}

	/*
	|--------------------------------------------------------------------------
	| Model Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Boot the model and define the event listeners.
	 *
	 * @return	void
	 */
	public static function boot()
	{
		parent::boot();

		// Get all the aliases
		$a = Config::get('app.aliases');

		// Setup the listeners
		static::setupEventListeners($a['NovaFormTab'], $a['NovaFormTabEventHandler']);
	}

}