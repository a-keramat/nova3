<?php

namespace Tests\Feature\Themes;

use Tests\TestCase;
use Illuminate\Http\Response;
use Nova\Authorization\Events;
use Silber\Bouncer\Database\Role;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = factory(Role::class)->create();
    }

    public function testUserCanViewEditRolePage()
    {
        $this->signInWithAbility('role.update');

        $this->get(route('roles.edit', $this->role))
            ->assertSuccessful();
    }

    public function testUnauthorizedUserCannotUpdateRole()
    {
        $this->get(route('roles.edit', $this->role))->assertRedirect(route('login'));
        $this->putJson(route('roles.update', $this->role), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testRoleCanBeUpdated()
    {
        $this->signInWithAbility('role.update');

        $data = [
            'name' => 'new-name',
            'title' => 'New title',
        ];

        $this->putJson(route('roles.update', $this->role), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('roles', $data);
    }

    public function testEventIsDispatchedWhenRoleIsUpdated()
    {
        Event::fake();

        $this->signInWithAbility('role.update');

        $data = [
            'name' => 'new-name',
            'title' => 'New title',
        ];

        $this->putJson(route('roles.update', $this->role), $data);

        $role = $this->role->fresh();

        Event::assertDispatched(Events\RoleUpdated::class, function ($event) use ($role) {
            return $event->role->is($role);
        });
    }
}
