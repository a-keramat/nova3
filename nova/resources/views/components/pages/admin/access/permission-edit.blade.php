<div v-cloak>
	<mobile>
		<p><a href="{{ route('admin.access.permissions') }}" class="btn btn-default btn-lg btn-block">Back to Permissions</a></p>
	</mobile>
	<desktop>
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="{{ route('admin.access.permissions') }}" class="btn btn-default">Back to Permissions</a>
			</div>
		</div>
	</desktop>
</div>

{!! Form::model($permission, ['route' => ['admin.access.permissions.update', $permission->id], 'class' => 'form-horizontal', 'method' => 'put']) !!}
	<div class="form-group{{ ($errors->has('display_name')) ? ' has-error' : '' }}">
		<label class="col-md-2 control-label">Name</label>
		<div class="col-md-5">
			{!! Form::text('display_name', null, ['class' => 'form-control input-lg']) !!}
			{!! $errors->first('display_name', '<p class="help-block">:message</p>') !!}
		</div>
	</div>

	<div class="form-group{{ ($errors->has('name')) ? ' has-error' : '' }}">
		<label class="col-md-2 control-label">Key</label>
		<div class="col-md-3">
			{!! Form::text('name', null, ['class' => 'form-control input-lg', 'v-model' => 'key', 'v-on:change' => 'updateKey']) !!}
			{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-5 col-md-offset-2">
			<p class="help-block">Permission keys must be in the format of <code>component.action</code>, like <code>page.create</code>. If you need to provide levels within an action, simply append the level number to the end of the action: <code>post.edit.2</code>.</p>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-2 control-label">Description</label>
		<div class="col-md-6">
			{!! Form::textarea('description', null, ['class' => 'form-control input-lg', 'rows' => 4]) !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-5 col-md-offset-2" v-cloak>
			<mobile>
				<p>{!! Form::button("Update Permission", ['class' => 'btn btn-primary btn-lg btn-block', 'type' => 'submit']) !!}</p>
			</mobile>
			<desktop>
				<div class="btn-toolbar">
					<div class="btn-group">
						{!! Form::button("Update Permission", ['class' => 'btn btn-primary btn-lg', 'type' => 'submit']) !!}
					</div>
				</div>
			</desktop>
		</div>
	</div>
{!! Form::close() !!}