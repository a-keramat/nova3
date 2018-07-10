<?php

namespace Nova\Authorize\Jobs;

use Illuminate\Bus\Queueable;
use Nova\Authorize\Permission;
use Nova\Foundation\Jobs\BaseJob;
use Nova\Foundation\Traits\BustsCache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreatePermissionJob extends BaseJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, BustsCache;

	protected $modelClass = Permission::class;
	protected $description = 'creating a permission';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		Permission::create($this->data);

		$this->refreshCacheForever('nova.permissions', function () {
			return Permission::with('roles')->get();
		});
	}
}
