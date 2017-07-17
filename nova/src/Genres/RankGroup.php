<?php namespace Nova\Genres;

use Eloquent;
use Nova\Foundation\Data\Reorderable;

class RankGroup extends Eloquent
{
	use Reorderable;

	protected $table = 'ranks_groups';
	protected $fillable = ['name', 'order', 'display'];
}