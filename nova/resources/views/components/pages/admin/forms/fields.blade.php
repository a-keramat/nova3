<div class="page-header">
	<h1>Form Fields <small>{!! $form->present()->name !!}</small></h1>
</div>

<div v-cloak>
	<mobile>
		@can('manage', $form)
			<p><a href="{{ route('admin.forms') }}" class="btn btn-default btn-lg btn-block">{!! icon('arrow-back') !!}<span>Back to Forms</span></a></p>
		@endcan
		@can('create', $field)
			<p><a href="{{ route('admin.forms.fields.create', $form->key) }}" class="btn btn-success btn-lg btn-block">{!! icon('add') !!}<span>Add a Field</span></a></p>
		@endcan
	</mobile>
	<desktop>
		<div class="btn-toolbar">
			@can('manage', $form)
				<div class="btn-group">
					<a href="{{ route('admin.forms') }}" class="btn btn-default">{!! icon('arrow-back') !!}<span>Back to Forms</span></a>
				</div>
			@endcan
			@can('create', $field)
				<div class="btn-group">
					<a href="{{ route('admin.forms.fields.create', $form->key) }}" class="btn btn-success">{!! icon('add') !!}<span>Add a Field</span></a>
				</div>
			@endcan
		</div>
	</desktop>
</div>

{!! alert('info', "Due to the additional controls necessary for field management, the form displayed below is not an accurate depiction of the final form. For an accurate preview of this form, use the ".link_to_route('admin.forms.preview', 'form preview', [$form->key]).".") !!}

@if ($unboundFields->count() > 0)
	{!! partial('form/form-fields-manage', ['fields' => $unboundFields, 'form' => $form]) !!}
@endif

@if ($unboundSections->count() > 0)
	@foreach ($unboundSections as $section)
		<fieldset>
			<legend>
				{!! $section->present()->name !!}
				<small>{!! $section->present()->statusAsLabel !!}</small>
			</legend>

			@if ($section->fieldsAll->count() > 0)
				{!! partial('form/form-fields-manage', ['fields' => $section->fieldsAll, 'form' => $form]) !!}
			@endif
		</fieldset>
	@endforeach
@endif

@if ($parentTabs->count() > 0)
	<ul class="nav nav-tabs">
	@foreach ($parentTabs as $tab)
		<li>
			<a href="#{{ $tab->link_id }}" data-toggle="tab">
				{!! $tab->present()->name !!}
				{!! $tab->present()->statusAsLabel !!}
			</a>
		</li>
	@endforeach
	</ul>

	<div class="tab-content">
	@foreach ($parentTabs as $tab)
		<div class="tab-pane" id="{{ $tab->link_id }}">
			@if ($tab->fieldsUnboundAll->count() > 0)
				{!! partial('form/form-fields-manage', ['fields' => $tab->fieldsUnboundAll, 'form' => $form]) !!}
			@endif

			@if ($tab->sectionsAll->count() > 0)
				@foreach ($tab->sectionsAll as $section)
					<fieldset>
						<legend>
							{!! $section->present()->name !!}
							<small>{!! $section->present()->statusAsLabel !!}</small>
						</legend>

						@if ($section->fieldsAll->count() > 0)
							{!! partial('form/form-fields-manage', ['fields' => $section->fieldsAll, 'form' => $form]) !!}
						@endif
					</fieldset>
				@endforeach
			@endif

			@if ($tab->childrenTabsAll->count() > 0)
				<ul class="nav nav-pills">
				@foreach ($tab->childrenTabsAll as $childTab)
					<li>
						<a href="#{{ $childTab->link_id }}" data-toggle="tab">
							{!! $childTab->present()->name !!}
							{!! $childTab->present()->statusAsLabel !!}
						</a>
					</li>
				@endforeach
				</ul>

				<div class="tab-content">
				@foreach ($tab->childrenTabsAll as $childTab)
					<div class="tab-pane" id="{{ $childTab->link_id }}">
						@if ($childTab->fieldsUnboundAll->count() > 0)
							{!! partial('form/form-fields-manage', ['fields' => $childTab->fieldsUnboundAll, 'form' => $form]) !!}
						@endif

						@if ($childTab->sectionsAll->count() > 0)
							@foreach ($childTab->sectionsAll as $section)
								<fieldset>
									<legend>
										{!! $section->present()->name !!}
										<small>{!! $section->present()->statusAsLabel !!}</small>
									</legend>

									@if ($section->fieldsAll->count() > 0)
										{!! partial('form/form-fields-manage', ['fields' => $section->fieldsAll, 'form' => $form]) !!}
									@endif
								</fieldset>
							@endforeach
						@endif
					</div>
				@endforeach
				</div>
			@endif
		</div>
	@endforeach
	</div>
@endif

@if ($unboundFields->count() == 0 and $unboundSections->count() == 0 and $parentTabs->count() == 0)
	{!! alert('warning', "There are no tabs, sections, or fields associated with this form. Start designing your form now!") !!}
@endif

@can('remove', $field)
	{!! modal(['id' => "removeField", 'header' => "Remove Form Field"]) !!}
@endcan