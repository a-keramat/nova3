<?php

use Inertia\Inertia;

try {
    $pages = cache()->rememberForever('nova.pages', function () {
        return \Nova\Pages\Page::get();
    });

    $pages->each(function ($page) use ($router) {
        $router
            ->{$page->verb}($page->uri, $page->resource)
            ->name($page->key);
    });
} catch (Exception $ex) {
    // We're not going to do anything here yet
}

Route::get('test', function () {
    Inertia::setRootView('app-client');

    return Inertia::render('Test');
});
