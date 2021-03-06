@extends ("artificer::layout")

@section ("component")
    <div class="form-group">
        {{ Form::label($options->label->for, $options->label->text, (array) $options->label->attributes) }}
        @foreach ($options->input as $box)
            <div>
                {{ Form::checkbox($box->name, $box->value, $box->checked, (array) $box->attributes) }} {{ $box->text }}
            </div>
        @endforeach
    </div>
@endsection
