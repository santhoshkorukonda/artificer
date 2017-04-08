@extends ("fartisan::layout")

@section ("component")
    <div class="form-group">
        {{ Form::label($options->label->for, $options->label->text, (array) $options->label->attributes) }}
        {{ Form::select($options->input->name, (array) $options->input->list, $options->input->value, array_merge(['class' => 'form-control'], (array) $options->input->attributes)) }}
    </div>
@endsection
