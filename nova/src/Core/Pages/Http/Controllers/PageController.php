<?php namespace Nova\Core\Pages\Http\Controllers;

use Page,
	BaseController,
	MenuRepositoryInterface,
	PageRepositoryInterface,
	EditPageRequest, CreatePageRequest, RemovePageRequest;
use Nova\Core\Pages\Events;
use Illuminate\Contracts\Foundation\Application;

class PageController extends BaseController {

	protected $repo;
	protected $menuRepo;

	public function __construct(Application $app, PageRepositoryInterface $repo,
			MenuRepositoryInterface $menus)
	{
		parent::__construct($app);

		$this->repo = $repo;
		$this->menuRepo = $menus;

		$this->middleware('auth');
	}

	public function index()
	{
		$this->data->page = $page = new Page;

		$this->authorize('manage', $page, "You do not have permission to manage pages.");

		$this->view = 'admin/pages/pages';
		$this->jsView = 'admin/pages/pages-js';
	}

	public function create()
	{
		$this->authorize('create', new Page, "You do not have permission to create pages.");

		$this->view = 'admin/pages/page-create';
		$this->jsView = 'admin/pages/page-create-js';

		$this->data->httpVerbs = [
			'GET' => 'GET',
			'POST' => 'POST',
			'PUT' => 'PUT',
			'DELETE' => 'DELETE',
		];

		$this->data->menus[0] = "No menu";
		$this->data->menus += $this->menuRepo->listAll('name', 'id');
	}

	public function store(CreatePageRequest $request)
	{
		$this->authorize('create', new Page, "You do not have permission to create pages.");

		// Create the page
		$page = $this->repo->create($request->all());

		// Fire the event
		event(new Events\PageWasCreated($page));

		// Set the flash message
		flash()->success("Page Created!", "Don't forget to update your menus with your new page.");

		return redirect()->route('admin.pages');
	}

	public function edit($pageId)
	{
		$page = $this->data->page = $this->repo->find($pageId);

		$this->authorize('edit', $page, "You do not have permission to edit pages.");

		$this->view = 'admin/pages/page-edit';
		$this->jsView = 'admin/pages/page-edit-js';

		$this->data->httpVerbs = [
			'GET' => 'GET',
			'POST' => 'POST',
			'PUT' => 'PUT',
			'DELETE' => 'DELETE',
		];

		$this->data->menus[0] = "No menu";
		$this->data->menus += $this->menuRepo->listAll('name', 'id');
	}

	public function update(EditPageRequest $request, $pageId)
	{
		$page = $this->repo->find($pageId);

		$this->authorize('edit', $page, "You do not have permission to edit pages.");

		// Update the page
		$page = $this->repo->update($page, $request->all());

		// Fire the event
		event(new Events\PageWasUpdated($page));

		// Set the flash message
		flash()->success("Page Updated!");

		return redirect()->route('admin.pages');
	}

	public function remove($pageId)
	{
		$this->isAjax = true;

		// Grab the page we're removing
		$page = $this->repo->find($pageId);

		if (policy($page)->remove($this->user))
		{
			// Build the body based on whether we found the page or not
			$body = ($page)
				? view(locate('page', 'admin/pages/page-remove'), compact('page'))
				: alert('danger', "Page not found.");
		}
		else
		{
			$body = alert('danger', "You do not have permission to remove pages.");
		}

		return partial('modal-content', [
			'header' => "Remove Page",
			'body' => $body,
			'footer' => false,
		]);
	}

	public function destroy(RemovePageRequest $request, $pageId)
	{
		$this->authorize('remove', new Page, "You do not have permission to remove pages.");

		// Delete the page
		$page = $this->repo->delete($pageId);

		// Fire the event
		event(new Events\PageWasDeleted($page->name, $page->key, $page->uri));

		// Set the flash message
		flash()->success("Page Removed!");

		return redirect()->route('admin.pages');
	}

	public function checkPageKey()
	{
		$this->isAjax = true;

		$count = $this->repo->countBy('key', request('key'));

		if ($count > 0)
		{
			return json_encode(['code' => 0]);
		}

		return json_encode(['code' => 1]);
	}

	public function checkPageUri()
	{
		$this->isAjax = true;

		$count = $this->repo->countBy('uri', request('uri'));

		if ($count > 0)
		{
			return json_encode(['code' => 0]);
		}

		return json_encode(['code' => 1]);
	}

}
