<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ $title }}</title>
		
		<meta name="description" content="{{ $settings->meta_description }}">
		<meta name="keywords" content="{{ $settings->meta_keywords }}">
		<meta name="author" content="{{ $settings->meta_author }}">
		
		<!-- Bootstrap styles -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">

		<!-- Web fonts styles -->
		{{ HTML::style('nova/assets/css/fonts.css') }}
		{{ HTML::style('nova/assets/css/fonts.core.css') }}

		<!-- Nova's base styles and any user-defined styles -->
		@if (is_file(APPPATH.'views/'.$skin.'/design/style.css'))
			{{ HTML::style('app/views/'.$skin.'/design/style.css') }}
		@else
			{{ HTML::style('nova/views/design/style.css') }}
		@endif
		
		<!-- Nova's base login styles and any user-defined styles -->
		@if (is_file(APPPATH.'views/'.$skin.'/design/style.login.css'))
			{{ HTML::style('app/views/'.$skin.'/design/style.login.css') }}
		@else
			{{ HTML::style('nova/views/design/style.login.css') }}
			
			@if (is_file(APPPATH.'views/'.$skin.'/design/custom.login.css'))
				{{ HTML::style('app/views/'.$skin.'/design/custom.login.css') }}
			@endif
		@endif
	</head>
	<body>
		{{ $template }}

		<!-- Nova's core Javascript -->
		<?php include NOVAPATH.'views/components/js/core/login_js.php';?>

		<!-- Nova's per-page Javascript -->
		{{ $javascript }}
	</body>
</html>