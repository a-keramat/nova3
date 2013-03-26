<?php namespace Nova\Core\Model;

use Model;

class Award extends Model {

	public $timestamps = false;

	protected $table = 'awards';

	protected $fillable = array(
		'name', 'category_id', 'image', 'order', 'desc', 'type', 'status',
	);

	protected static $properties = array(
		'id', 'name', 'category_id', 'image', 'order', 'desc', 'type', 'status',
	);

	/**
	 * Belongs To: Award Category
	 */
	public function category()
	{
		return $this->belongsTo('AwardCategory');
	}

	/**
	 * Has Many: Recipients
	 */
	public function recipients()
	{
		return $this->hasMany('AwardRecipient');
	}

}