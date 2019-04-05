<?php

namespace Nova\Authorization\Http\Authorizers;

use Silber\Bouncer\Database\Role;
use Nova\Foundation\Http\Authorizers\BaseAuthorizer;

class Edit extends BaseAuthorizer
{
    public function authorize()
    {
        $role = Role::find($this->route('role'))->first();

        return $role && $this->user()->can('update', $role);
    }
}
