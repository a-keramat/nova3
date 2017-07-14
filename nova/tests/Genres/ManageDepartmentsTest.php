<?php namespace Tests\Genres;

use Tests\DatabaseTestCase;

class ManageDepartmentsTest extends DatabaseTestCase
{
	protected $department;

	public function setUp()
	{
		parent::setUp();

		$this->department = create('Nova\Genres\Department');
	}

	/** @test **/
	public function unauthorized_users_cannot_manage_departments()
	{
		$this->withExceptionHandling();

		$this->get(route('departments.index'))->assertRedirect(route('login'));
		$this->get(route('departments.create'))->assertRedirect(route('login'));
		$this->post(route('departments.store'))->assertRedirect(route('login'));
		$this->get(route('departments.edit', $this->department))->assertRedirect(route('login'));
		$this->patch(route('departments.update', $this->department))->assertRedirect(route('login'));
		$this->delete(route('departments.destroy', $this->department))->assertRedirect(route('login'));
		$this->get(route('departments.reorder'))->assertRedirect(route('login'));
		$this->patch('/admin/departments/reorder')->assertRedirect(route('login'));

		$this->signIn();

		$this->get(route('departments.index'))->assertStatus(403);
		$this->get(route('departments.create'))->assertStatus(403);
		$this->post(route('departments.store'))->assertStatus(403);
		$this->get(route('departments.edit', $this->department))->assertStatus(403);
		$this->patch(route('departments.update', $this->department))->assertStatus(403);
		$this->delete(route('departments.destroy', $this->department))->assertStatus(403);
		$this->get(route('departments.reorder'))->assertStatus(403);
		$this->patch('/admin/departments/reorder')->assertStatus(403);
	}

	/** @test **/
	public function a_department_can_be_created()
	{
		$admin = $this->createAdmin();

		$this->signIn($admin);

		$department = make('Nova\Genres\Department');

		$this->post(route('departments.store'), $department->toArray());

		$this->assertDatabaseHas('departments', [
			'name' => $department->name,
			'description' => $department->description
		]);
	}

	/** @test **/
	public function a_department_can_be_updated()
	{
		$admin = $this->createAdmin();

		$this->signIn($admin);

		$this->patch(
			route('departments.update', [$this->department]),
			['name' => "New Name"]
		);

		$this->assertDatabaseHas('departments', ['name' => "New Name"]);
	}

	/** @test **/
	public function a_department_can_be_deleted()
	{
		$admin = $this->createAdmin();

		$this->signIn($admin);

		$this->delete(route('departments.destroy', [$this->department]));

		$this->assertDatabaseMissing('departments', ['name' => $this->department->name]);
	}

	/** @test **/
	public function a_department_can_be_reordered()
	{
		$admin = $this->createAdmin();

		$this->signIn($admin);

		$response = $this->patch('/admin/departments/reorder', ['depts' => [2,1,3,4,5,6,7,8,9,10,11,12,13,14]]);

		$response->assertStatus(200);

		$this->assertDatabaseHas('departments', ['id' => 2, 'order' => 0]);
		$this->assertDatabaseHas('departments', ['id' => 1, 'order' => 1]);
	}
}