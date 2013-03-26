<?php namespace Nova\Core\Model;

use Model;
use Config;

class Rank extends Model {

	protected $table = 'ranks_';

	protected $fillable = array(
		'info_id', 'group_id', 'base', 'pip',
	);
	
	protected static $properties = array(
		'id', 'info_id', 'group_id', 'base', 'pip', 'created_at', 'updated_at',
	);

	/**
	 * Belongs To: Rank Info
	 */
	public function info()
	{
		return $this->belongsTo('RankInfo');
	}

	/**
	 * Belongs To: Rank Group
	 */
	public function group()
	{
		return $this->belongsTo('RankGroup');
	}

	/**
	 * Has Many: Characters
	 */
	public function characters()
	{
		return $this->hasMany('Character');
	}

	/**
	 * Since the table name is appended with the genre, we can't hard-code
	 * it in to the model. When the object is created, we have to pull the
	 * genre out of the config and name the table.
	 *
	 * @internal
	 * @return	void
	 */
	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);

		// Set the name of the table
		$this->setTable('ranks_'.Config::get('nova.genre'));
	}

}