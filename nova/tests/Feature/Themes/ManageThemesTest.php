<?php

namespace Tests\Feature\Themes;

use Tests\TestCase;
use Nova\Themes\Jobs;
use Nova\Themes\Theme;
use Nova\Themes\Events;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageThemesTest extends TestCase
{
    use RefreshDatabase;

    protected $theme;

    public function setUp(): void
    {
        parent::setUp();

        $this->theme = factory(Theme::class)->create();
    }

    public function testUserCanViewManageThemesPage()
    {
        $this->signInWithAbility('theme.create');

        $this->get(route('themes.index'))
            ->assertSuccessful();
    }

    public function testUnauthorizedUserCannotManageThemes()
    {
        $this->get(route('themes.index'))->assertRedirect(route('login'));
    }

    /** @test **/
    public function a_user_can_view_the_edit_theme_page()
    {
        $this->signIn();

        $this->get(route('themes.edit', $this->theme))
            ->assertSuccessful();
    }

    /** @test **/
    public function a_user_can_edit_a_theme()
    {
        $this->signIn();

        $this->followingRedirects()
            ->from(route('themes.edit', $this->theme))
            ->put(route('themes.update', $this->theme), [
                'name' => 'New Name',
                'location' => $this->theme->location,
                'layout_auth' => $this->theme->layout_auth,
                'layout_admin' => $this->theme->layout_admin,
                'layout_public' => $this->theme->layout_public,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('themes', [
            'id' => $this->theme->id,
            'name' => 'New Name',
        ]);
    }

    /** @test **/
    public function an_event_is_dispatched_when_a_theme_is_edited()
    {
        Event::fake();

        $data = factory(Theme::class)->make()->toArray();

        $theme = Jobs\EditThemeJob::dispatchNow($this->theme, $data);

        Event::assertDispatched(Events\ThemeUpdated::class, function ($event) use ($theme) {
            return $event->theme->is($theme);
        });
    }
}
