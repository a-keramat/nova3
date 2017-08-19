@extends('layouts.welcome')

@section('content')
	<div class="title m-b-md">
		Status
	</div>

	<ul class="nav nav-pills nav-justified mb-3" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#aries" role="tab">NextGen Aries</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#gemini" role="tab">NextGen Gemini</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#orion" role="tab">NextGen Orion</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#beta1" role="tab">3.0 Beta 1</a>
		</li>
	</ul>

	<div class="tab-content">
		<div id="aries" class="tab-pane active" role="tabpanel">
			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Characters</span>
						<i class="fal fa-code fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card text-white bg-success">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start">
						<span class="mr-auto">Genre Assets</span>
						<i class="fal fa-check-circle fa-fw"></i>
					</h3>

					<ul>
						<li>Authorized users can create, delete, reorder, and update departments</li>
						<li>Authorized users can create, delete, reorder, and update positions</li>
						<li>Authorized users can create, delete, reorder, duplicate, and update rank groups</li>
						<li>Authorized users can create, delete, reorder, and update rank info</li>
						<li>Authorized users can create, delete, reorder, dupliate, and update ranks</li>
						<li>Genre data for departments, positions, rank groups, rank info, and ranks is inserted during install</li>
					</ul>
				</div>
			</div>

			<div class="card text-white bg-success">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start">
						<span class="mr-auto">Users</span>
						<i class="fal fa-check-circle fa-fw"></i>
					</h3>

					<ul>
						<li>Authorized users can create, update, delete, and restore users</li>
						<li>Authorized users can force a password reset</li>
						<li>Authenticated users can view the profile of any user</li>
						<li>Users can update their user profile</li>
						<li>Users can update their password</li>
					</ul>
				</div>
			</div>

			<div class="card text-white bg-success">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start">
						<span class="mr-auto">Authorization</span>
						<i class="fal fa-check-circle fa-fw"></i>
					</h3>

					<ul>
						<li>Authorized users can create, update, and delete roles</li>
						<li>Authorized users can create, update, and delete permissions</li>
					</ul>
				</div>
			</div>

			<div class="card text-white bg-success">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start">
						<span class="mr-auto">Authentication</span>
						<i class="fal fa-check-circle fa-fw"></i>
					</h3>

					<ul>
						<li>Users can sign in</li>
						<li>Users can sign out</li>
						<li>Users can reset their password</li>
						<li>Users are locked out for 30 minutes after 5 unsuccessful sign in attempts</li>
						<li>If an admin forces a reset, the user is redirected to the password reset screen on their next sign in attempt</li>
					</ul>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Setup</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>
		</div>

		<div id="orion" class="tab-pane">
			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Dynamic Forms</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Settings</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Application Review Center</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>
		</div>

		<div id="gemini" class="tab-pane">
			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Themes</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Stories</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Private Messages</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>
		</div>

		<div id="beta1" class="tab-pane">
			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Extensions</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h3 class="d-flex align-items-center justify-content-start mb-0">
						<span class="mr-auto">Menus</span>
						<i class="fal fa-road fa-fw"></i>
					</h3>
				</div>
			</div>
		</div>
	</div>
@endsection