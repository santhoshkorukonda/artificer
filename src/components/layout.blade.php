@if (! empty($options->row->start))
    <div class="row">
@endif

<div class="{{ $options->wrapper->class }}" {{ Html::attributes((array) $options->wrapper->attributes) }}>
    @yield ("component")
</div>

@if (! empty($options->row->end))
    </div>
@endif
