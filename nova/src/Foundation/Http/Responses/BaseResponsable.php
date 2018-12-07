<?php

namespace Nova\Foundation\Http\Responses;

use Nova\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nova\Foundation\Application;
use Illuminate\Contracts\Support\Responsable;
use Nova\Foundation\Events\NovaResponseEvent;

abstract class BaseResponsable implements Responsable
{
	protected $app;
	protected $data;
	protected $page;
	protected $theme;
	protected $output;

	public function __construct($theme, Page $page, Application $app)
	{
		$this->app = $app;
		$this->page = $page;
		$this->theme = $theme;
	}

	/**
	 * Get the name of the current route from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string
	 */
	public function getRouteName(Request $request)
	{
		return $request->route()->getName();
	}

	/**
	 * Handle preparing the data to be used by the view.
	 *
	 * This is a method that's meant to be used in the individual Responsable
	 * objects, especially by developers that want to make some changes to
	 * the data before Nova takes over again and uses that data.
	 *
	 * @return array
	 */
	public function prepareData() : array
	{
		return $this->data;
	}

	/**
	 * Handle converting this to a response object that Laravel knows what
	 * to do with.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	final public function toResponse($request)
	{
		$this->data = $this->prepareData();

		$this->passDataToContainer();

		$this->fireNovaResponseEvent($request);

		if ($request->expectsJson()) {
			return response()->json($this->data, Response::HTTP_OK);
		}

		return response($this->render(), Response::HTTP_OK);
	}

	/**
	 * The list of views for this response.
	 *
	 * @return array
	 */
	public function views() : array
	{
		return [];
	}

	/**
	 * Any data that should be sent with the response.
	 *
	 * @param  array  $data
	 * @return \Nova\Foundation\Http\Responses\BaseResponsable
	 */
	public function with(array $data = [])
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Fire an event that can be listened for as part of the response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Nova\Foundation\Http\Responses\BaseResponsable
	 */
	protected function fireNovaResponseEvent(Request $request)
	{
		$result = event(new NovaResponseEvent($this->getRouteName($request), $this->data));

		if ($result) {
			$this->data = $result[0];
		}

		return $this;
	}

	/**
	 * Pass the final data off to the container for use in the views.
	 *
	 * @return \Nova\Foundation\Http\Responses\BaseResponsable
	 */
	protected function passDataToContainer()
	{
		$this->app['nova.response.data'] = $this->data;

		return $this;
	}

	/**
	 * Get a specific view name.
	 *
	 * @param  string  $view
	 * @return string
	 */
	protected function getView($view)
	{
		$views = array_merge([
			'structure' => 'app',
			'layout' => $this->page->layout,
			'template' => $this->page->content_template,
			'page' => null,
			'script' => null,
		], $this->views() ?? []);

		return data_get($views, $view, null);
	}

	/**
	 * Handle rendering the final output.
	 *
	 * @return string
	 */
	protected function render()
	{
		$this
			->buildThemeStructure()
			->buildThemeLayout()
			->buildThemeTemplate()
			->buildPage()
			->buildScripts()
			->buildSpriteMap();

		return $this->output;
	}

	protected function buildThemeStructure()
	{
		$this->output = $this->theme->structure($this->getView('structure'), []);

		return $this;
	}

	protected function buildThemeLayout()
	{
		$this->output = $this->theme->layout($this->getView('layout'), []);

		return $this;
	}

	protected function buildThemeTemplate()
	{
		$this->output = $this->theme->template($this->getView('template'), []);

		return $this;
	}

	protected function buildPage()
	{
		$this->output = $this->theme->page($this->getView('page'), $this->data);

		return $this;
	}

	protected function buildScripts()
	{
		$this->output = $this->theme->scripts((array)$this->getView('script'));

		return $this;
	}

	protected function buildSpriteMap()
	{
		$this->output = $this->theme->spriteMap();

		return $this;
	}
}