<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} {{$class}}">

        @include('admin::form.error')
        <div class="{{$class}}">
            <uploader ref="uploader" field="{{$name}}" path="{{$path}}" :max-count="{{$maxCount}}"
                      accept="{{$accept}}" :files="{{$files}}"></uploader>
        </div>
        <input type="hidden" name="{{$name}}" value="{{$value}}">
        @include('admin::form.help-block')

    </div>
</div>
