@extends ("fartisan::layout")

@section ("component")
    <div class="form-group">
        {{ Form::label($options->label->for, $options->label->text, (array) $options->label->attributes) }}
        <div class="input-group">
            {{ Form::text($options->input->name, $options->input->value, array_merge(['class' => 'form-control'], (array) $options->input->attributes)) }}
            <span class="input-group-addon">{{ $options->input->addontext }}</span>
        </div>
    </div>
@endsection
