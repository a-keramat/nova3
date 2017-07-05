@extends('layouts.app')

@section('title', _m('users'))

@section('content')
	<h1>{{ _m('users') }}</h1>

	<mobile>
		<div class="row">
			@can('create', $userClass)
				<div class="col">
					<p><a href="{{ route('users.create') }}" class="btn btn-success btn-block">{{ _m('user-add') }}</a></p>
				</div>
			@endcan
		</div>
	</mobile>

	<desktop>
		<div class="btn-toolbar">
			@can('create', $userClass)
				<div class="btn-group">
					<a href="{{ route('users.create') }}" class="btn btn-success">{{ _m('user-add') }}</a>
				</div>
			@endcan
		</div>
	</desktop>

	@if ($users->count() > 0)
		<div class="row">
			<div class="col-md-4">
				<div class="form-group input-group">
					<input type="text"
						   class="form-control"
						   placeholder="{{ _m('user-find') }}"
						   v-model="search">
					<span class="input-group-btn">
						<a class="btn btn-secondary" href="#" @click.prevent="search = ''">
							{!! icon('close') !!}
						</a>
					</span>
				</div>
			</div>
		</div>

		<table class="table" v-cloak>
			<thead class="thead-default">
				<tr>
					<th colspan="2">{{ _m('name') }}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="user in filteredUsers">
					<td>@{{ user.displayName }}</td>
					<td>
						<div class="dropdown pull-right">
							<button class="btn btn-secondary"
									type="button"
									id="dropdownMenuButton"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								{!! icon('more') !!}
							</button>
							<div class="dropdown-menu dropdown-menu-right"
								 aria-labelledby="dropdownMenuButton">
								<a :href="'/admin/users/' + user.id + '/show'" class="dropdown-item">{!! icon('user') !!} Profile</a>

								@can('manage', $userClass)
									<div class="dropdown-divider"></div>
								@endcan
								
								@can('update', $userClass)
									<a :href="'/admin/users/' + user.id + '/edit'" class="dropdown-item">{!! icon('edit') !!} {{ _m('edit') }}</a>
								@endcan

								@can('delete', $userClass)
									<a href="#" class="dropdown-item text-danger" :data-user="user.id" @click.prevent="deleteUser" v-if="!isTrashed(user)">{!! icon('delete') !!} {{ _m('delete') }}</a>
								@endcan

								@can('update', $userClass)
									<a href="#" class="dropdown-item text-success" :data-user="user.id" @click.prevent="restoreUser" v-if="isTrashed(user)">{!! icon('undo') !!} {{ _m('restore') }}</a>
								@endcan
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	@else
		<div class="alert alert-warning">
			{{ _m('user-error-not-found') }} <a href="{{ route('users.create') }}" class="alert-link">{{ _m('user-error-add') }}
		</div>
	@endif
@endsection

@section('js')
	<script>
		vue = {
			data: {
				users: {!! $users !!},
				search: ''
			},

			computed: {
				filteredUsers () {
					let self = this

					return self.users.filter(function (user) {
						let regex = new RegExp(self.search, 'i')

						return regex.test(user.name)
							|| regex.test(user.email)
							|| regex.test(user.nickname)
							// || regex.test(user.status)
					})
				}
			},

			methods: {
				deleteUser (event) {
					let confirm = window.confirm("{{ _m('user-delete') }}")

					if (confirm) {
						let user = event.target.getAttribute('data-user')

						axios.delete('/admin/users/' + user)

						window.location.replace('/admin/users')
					}
				},

				isTrashed (user) {
					return user.deleted_at != null
				},

				restoreUser (event) {
					let user = event.target.getAttribute('data-user')

					axios.patch('/admin/users/' + user + '/restore')

					window.location.replace('/admin/users')
				}
			}
		}
	</script>
@endsection