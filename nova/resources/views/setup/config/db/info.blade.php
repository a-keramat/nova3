@extends('layouts.setup')

@section('title', 'Database Connection')

@section('header', 'Database Connection')

@section('content')
	<h1>Configure Your Database Connection</h1>
	<h3>Tell us a little bit about the database {{ config('nova.app.name') }} is being installed into</h3>

	<div class="row justify-content-around">
		<div class="col-lg-10">
			<div class="card-deck">
				@if (in_array('mysql', PDO::getAvailableDrivers()))
					<div :class="cardClassName('mysql')">
						<div class="card-body">
							<a role="button" @click="driver = 'mysql'">
								<div class="logo mysql"></div>
							</a>
						</div>
					</div>
				@endif

				<div :class="cardClassName('mariadb')">
					<div class="card-body">
						<a role="button" @click="driver = 'mariadb'">
							<div class="logo mariadb"></div>
						</a>
					</div>
				</div>

				@if (in_array('pgsql', PDO::getAvailableDrivers()))
					<div :class="cardClassName('pgsql')">
						<div class="card-body">
							<a role="button" @click="driver = 'pgsql'">
								<div class="logo postgresql"></div>
							</a>
						</div>
					</div>
				@endif

				@if (in_array('sqlite', PDO::getAvailableDrivers()))
					<div :class="cardClassName('sqlite')">
						<div class="card-body">
							<a role="button" @click="driver = 'sqlite'">
								<div class="logo sqlite"></div>
							</a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>

	<div v-cloak>
		{!! Form::open(['route' => "setup.{$_setupType}.config.db.check"]) !!}
			{!! Form::hidden('db_driver', null, ['v-model' => 'driver']) !!}
			<div v-show="driver && driver != 'sqlite'">
				<div class="form-group row justify-content-around" v-show="driver == 'mysql'">
					<div class="col-lg-10">
						<h2>MySQL</h2>

						<p>MySQL is the database system all previous versions of Nova have used and is one of the most widely available database systems in the world. Most shared hosts have MySQL installed by default so this is most often the best option for running {{ config('nova.app.name') }}. If you have questions about MySQL, get in touch with your web host.</p>
					</div>
				</div>

				<div class="form-group row justify-content-around" v-show="driver == 'mariadb'">
					<div class="col-lg-10">
						<h2>MariaDB</h2>

						<p>MySQL is the database system all previous versions of Nova have used and is one of the most widely available database systems in the world. Most shared hosts have MySQL installed by default so this is most often the best option for running {{ config('nova.app.name') }}. If you have questions about MySQL, get in touch with your web host.</p>
					</div>
				</div>

				<div class="form-group row justify-content-around" v-show="driver == 'pgsql'">
					<div class="col-lg-10">
						<h2>PostgreSQL</h2>

						<p>Though often considered to be geared more toward enterprise applications, PostgreSQL is a mature database system in the vein of Oracle. In most cases, PostgresSQL isn't available on shared hosts and will need to be installed separately. If you have questions about PostgresSQL, get in touch with your web host. <strong class="text-warning">This option is currently experimental.</strong></p>
					</div>
				</div>

				<div class="row justify-content-around">
					<div class="col-md-9 col-lg-8">
						<div class="form-group">
							<label>Host</label>
							<div class="control-wrapper">
								{!! Form::text('db_host', 'localhost', ['class' => 'form-control form-control-lg']) !!}
								<small class="form-text text-muted">For most web hosts, <code>localhost</code> will be correct. If you aren't sure, refer to the information you received from your web host or contact them directly.</small>
							</div>
						</div>
					</div>

					<div class="col-md-3 col-lg-2">&nbsp;</div>
				</div>

				<div class="row justify-content-around">
					<div class="col-md-6 col-lg-5">
						<div class="form-group">
							<label>Database Name</label>
							<div class="control-wrapper">
								{!! Form::text('db_name', false, ['class' => 'form-control form-control-lg']) !!}
							</div>
						</div>
					</div>

					<div class="col-md-6 col-lg-5">
						<div :class="tablePrefixClass">
							<label>Table Prefix</label>
							<div class="control-wrapper">
								{!! Form::text('db_prefix', false, ['class' => 'form-control form-control-lg', 'v-model' => 'prefix', '@change' => 'checkPrefix']) !!}
								<small v-if="prefixWarning" class="form-text">You cannot use the same database table prefix as your Nova 2 installation. Please choose a different table prefix.</small>
							</div>
						</div>
					</div>
				</div>

				<div class="row justify-content-around">
					<div class="col-md-6 col-lg-5">
						<div class="form-group">
							<label>Username</label>
							<div class="control-wrapper">
								{!! Form::text('db_user', false, ['class' => 'form-control form-control-lg']) !!}
							</div>
						</div>
					</div>

					<div class="col-md-6 col-lg-5">
						<div class="form-group">
							<label>Password</label>
							<div class="control-wrapper">
								{!! Form::password('db_password', ['class' => 'form-control form-control-lg']) !!}
							</div>
						</div>
					</div>
				</div>

				<div class="form-group row justify-content-around">
					<div class="col-lg-10">
						{!! Form::button('Create Database Connection', ['class' => 'btn btn-outline-primary', 'type' => 'submit']) !!}
					</div>
				</div>
			</div>

			<div v-show="driver == 'sqlite'">
				<div class="form-group row justify-content-around">
					<div class="col-lg-10">
						<h2>SQLite</h2>

						<p>SQLite is a file-based database that uses a single database file on the server. Because of this, a good rule of thumb is that you should avoid using SQLite in situations where the database will be accessed simultaneously from multiple locations. <strong class="text-danger">SQLite is only advised for development purposes.</strong></p>
					</div>
				</div>

				<div class="row justify-content-around">
					<div class="col-md-6 col-lg-5">
						<div class="form-group">
							<label>Table Prefix</label>
							{!! Form::text('db_prefix', $prefix, ['class' => 'form-control form-control-lg']) !!}
						</div>
					</div>
				</div>

				<div class="form-group row justify-content-around">
					<div class="col-lg-10">
						{!! Form::button('Create Database Connection', ['class' => 'btn btn-outline-primary', 'type' => 'submit']) !!}
					</div>
				</div>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@section('controls')
	@if (file_exists(app('path.config').'/database.php'))
		<a href="{{ route('setup.'.$_setupType.'.config.email') }}" class="btn btn-primary btn-lg">Next: Email Settings</a>
	@else
		<a class="btn btn-primary btn-lg disabled">Next: Email Settings</a>
	@endif

	@if ($_setupType == 'install')
		<a href="{{ route('setup.'.$_setupType) }}" class="btn btn-link-secondary btn-lg">Back: Fresh Install Info</a>
	@else
		<a href="{{ route('setup.'.$_setupType.'.config.nova2') }}" class="btn btn-link-secondary btn-lg">Back: Nova 2 Info</a>
	@endif
@endsection

@section('js')
	<script>
		vue = {
			data: {
				driver: false,
				prefix: "{{ $prefix }}",
				oldPrefix: "{{ config('nova2.db_prefix') }}",
				prefixWarning: false
			},
			computed: {
				tablePrefixClass: function () {
					if (this.prefixWarning) {
						return 'form-group has-danger'
					}
					return 'form-group'
				}
			},
			methods: {
				cardClassName: function (driverType) {
					if (this.driver == driverType) {
						return 'card card-outline-primary'
					}
					return 'card'
				},
				checkPrefix: function () {
					this.prefixWarning = false
					if (this.driver == 'mysql' && this.prefix == this.oldPrefix) {
						this.prefix = ""
						this.prefixWarning = true
					}
				}
			}
		}
	</script>
@endsection