@extends ("artificer::layout")

@section ("component")
    <div class="form-group">
        {{ Form::label($options->label->for, $options->label->text, (array) $options->label->attributes) }}
        <select name="{{ $options->input->name }}" {!! Html::attributes(array_merge(["class" => "form-control"], (array) $options->input->attributes)); !!}>
            {!! Artificer::compileOptions($options->input->database->uid) !!}
        </select>
    </div>
@endsection
