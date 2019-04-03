<?php

namespace Tests\Feature\Themes;

use Tests\TestCase;
use Nova\Themes\Jobs;
use Nova\Themes\Theme;
use Nova\Themes\Events;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThemeTest extends TestCase
{
    use RefreshDatabase;

    protected $theme;

    public function setUp(): void
    {
        parent::setUp();

        $this->theme = factory(Theme::class)->create();
    }

    public function testUserCanViewCreateThemePage()
    {
        $this->signInWithAbility('theme.create');

        $this->get(route('themes.create'))
            ->assertSuccessful();
    }

    public function testUnauthorizedUserCannotCreateTheme()
    {
        $this->get(route('themes.create'))->assertRedirect(route('login'));
        $this->post(route('themes.store'), [])->assertRedirect(route('login'));
    }

    public function testThemeCanBeCreated()
    {
        Storage::fake('themes');

        $this->signInWithAbility('theme.create');

        $theme = factory(Theme::class)->make()->toArray();

        $this->followingRedirects()
            ->from(route('themes.create'))
            ->post(route('themes.store'), $theme)
            ->assertSuccessful();

        $this->assertDatabaseHas('themes', $theme);
    }

    public function testThemeDirectoryIsScaffoldedWhenThemeIsCreated()
    {
        Storage::fake('themes');

        $data = factory(Theme::class)->make()->toArray();

        Jobs\CreateThemeJob::dispatchNow($data);

        $this->assertCount(1, Storage::disk('themes')->directories());
    }

    public function testEventIsDispatchedWhenThemeIsCreated()
    {
        Event::fake();
        Storage::fake('themes');

        $data = factory(Theme::class)->make()->toArray();

        $theme = Jobs\CreateThemeJob::dispatchNow($data);

        Event::assertDispatched(Events\ThemeCreated::class, function ($event) use ($theme) {
            return $event->theme->is($theme);
        });
    }

    public function testNameIsRequiredToCreateTheme()
    {
        Storage::fake('themes');

        $this->signIn();

        $this->from(route('themes.index'))
            ->post(route('themes.store'), [
                'name' => null,
                'location' => 'some-location',
            ])
            ->assertSessionHasErrors('name');
    }

    public function testLocationIsRequiredToCreateTheme()
    {
        Storage::fake('themes');

        $this->signIn();

        $this->from(route('themes.index'))
            ->post(route('themes.store'), [
                'name' => 'some-name',
                'location' => null,
            ])
            ->assertSessionHasErrors('location');
    }

    public function testAuthLayoutIsRequiredToCreateTheme()
    {
        Storage::fake('themes');

        $this->signIn();

        $this->from(route('themes.index'))
            ->post(route('themes.store'), [
                'layout_auth' => null,
            ])
            ->assertSessionHasErrors('layout_auth');
    }

    public function testAdminLayoutIsRequiredToCreateTheme()
    {
        Storage::fake('themes');

        $this->signIn();

        $this->from(route('themes.index'))
            ->post(route('themes.store'), [
                'layout_admin' => null,
            ])
            ->assertSessionHasErrors('layout_admin');
    }

    public function testPublicLayoutIsRequiredToCreateTheme()
    {
        Storage::fake('themes');

        $this->signIn();

        $this->from(route('themes.index'))
            ->post(route('themes.store'), [
                'layout_public' => null,
            ])
            ->assertSessionHasErrors('layout_public');
    }
}
