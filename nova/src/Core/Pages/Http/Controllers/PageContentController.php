<?php namespace Nova\Core\Pages\Http\Controllers;

use Flash,
	Input,
	BaseController,
	EditPageContentRequest,
	CreatePageContentRequest,
	RemovePageContentRequest,
	PageRepositoryInterface,
	PageContentRepositoryInterface;
use Nova\Core\Pages\Events\PageContentWasCreated,
	Nova\Core\Pages\Events\PageContentWasDeleted,
	Nova\Core\Pages\Events\PageContentWasUpdated;
use Illuminate\Contracts\Foundation\Application;

class PageContentController extends BaseController {

	protected $repo;
	protected $pagesRepo;

	public function __construct(Application $app, PageContentRepositoryInterface $repo,
			PageRepositoryInterface $pages)
	{
		parent::__construct($app);

		$this->repo = $repo;
		$this->pagesRepo = $pages;

		$this->middleware('auth');
	}

	public function index()
	{
		$this->view = 'admin/pages/content';
		$this->jsView = 'admin/pages/content-js';
	}

	public function create()
	{
		$this->view = 'admin/pages/content-create';
		$this->jsView = 'admin/pages/content-create-js';

		$this->data->types = [
			'header' => "Header",
			'message' => "Message",
			'title' => "Page Title",
			'other' => "Other",
		];
		
		$this->data->pages[""] = "No page";
		$this->data->pages += $this->pagesRepo->listAllBy('verb', 'GET', 'name', 'id');
	}

	public function store(CreatePageContentRequest $request)
	{
		// Create the content
		$content = $this->repo->create(Input::all());

		// Fire the event
		event(new PageContentWasCreated($content));

		// Set the flash message
		Flash::success("Page content has been created.");

		return redirect()->route('admin.content');
	}

	public function edit($contentId)
	{
		$this->view = 'admin/pages/content-edit';
		$this->jsView = 'admin/pages/content-edit-js';

		$this->data->content = $this->repo->find($contentId);
		$this->data->types = [
			'header' => "Header",
			'message' => "Message",
			'title' => "Page Title",
			'other' => "Other",
		];

		$this->data->pages[""] = "No page";
		$this->data->pages += $this->pagesRepo->listAllBy('verb', 'GET', 'name', 'id');
	}

	public function update(EditPageContentRequest $request, $contentId)
	{
		// Update the content
		$content = $this->repo->update($contentId, Input::all());

		// Fire the event
		event(new PageContentWasUpdated($content));

		// Set the flash message
		Flash::success("Page content has been updated.");

		return redirect()->route('admin.content');
	}

	public function remove($contentId)
	{
		$this->isAjax = true;

		// Grab the content we're removing
		$content = $this->repo->find($contentId);

		// Build the body based on whether we found the content or not
		$body = ($content)
			? view(locate('page', 'admin/pages/content-remove'), compact('content'))
			: alert('danger', "Page content not found.");

		return partial('modal-content', [
			'header' => "Remove Page Content",
			'body' => $body,
			'footer' => false,
		]);
	}

	public function destroy(RemovePageContentRequest $request, $contentId)
	{
		// Delete the content
		$content = $this->repo->delete($contentId);

		// Fire the event
		event(new PageContentWasDeleted($content->key, $content->type));

		// Set the flash message
		Flash::success("Page content has been removed.");

		return redirect()->route('admin.content');
	}

	public function checkContentKey()
	{
		$this->isAjax = true;

		$count = $this->repo->countBy('key', Input::get('key'));

		if ($count > 0)
		{
			return json_encode(['code' => 0]);
		}

		return json_encode(['code' => 1]);
	}

}
