@extends ("fartisan::layout")

@section ("component")
    <div class="form-group">
        {{ Form::label($options->label->for, $options->label->text, (array) $options->label->attributes) }}
        {{ Form::file($options->input->name, array_merge(['class' => 'form-control'], (array) $options->input->attributes)) }}
    </div>
@endsection
