@if (! empty($options->row->start))
    <div class="row">
@endif

@if (! empty($options->wrapper->start))
    <div class="{{ $options->wrapper->options->class }}" {{ Html::attributes((array) $options->wrapper->options->attributes) }}>
@endif

{{ Form::button($options->input->value, array_merge(["class" => "btn"], (array) $options->input->attributes)) }}

@if (! empty($options->wrapper->end))
    </div>
@endif

@if (! empty($options->row->end))
    </div>
@endif
