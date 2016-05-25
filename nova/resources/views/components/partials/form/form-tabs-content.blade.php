@if ($tabs->count() > 0)
	<div class="tab-content">
	@foreach ($tabs as $tab)
		<div class="tab-pane" id="{{ $tab->link_id }}">
			{!! $tab->present()->message !!}

			@if ($tab->fieldsUnbound->count() > 0)
				{!! partial('form/form-fields', ['fields' => $tab->fieldsUnbound, 'editable' => $editable, 'form' => $form, 'action' => $action, 'data' => $data, 'id' => $id, 'fieldNameWrapper' => $fieldNameWrapper]) !!}
			@endif

			{!! partial('form/form-sections', ['sections' => $tab->sections, 'editable' => $editable, 'form' => $form, 'action' => $action, 'data' => $data, 'id' => $id, 'fieldNameWrapper' => $fieldNameWrapper]) !!}

			@if ($tab->childrenTabs->count() > 0)
				{!! partial('form/form-tabs-control', ['tabs' => $tab->childrenTabs, 'style' => 'pills', 'editable' => $editable, 'form' => $form, 'action' => $action, 'data' => $data, 'id' => $id]) !!}

				{!! partial('form/form-tabs-content', ['tabs' => $tab->childrenTabs, 'editable' => $editable, 'form' => $form, 'action' => $action, 'data' => $data, 'id' => $id, 'fieldNameWrapper' => $fieldNameWrapper]) !!}
			@endif
		</div>
	@endforeach
	</div>
@endif