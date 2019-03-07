@extends('layouts.app')
@section('styles')
@endsection
@section('title', __('Добавление нового мультилинка'))
@section('content')
    <div>
        <a href="{{ route('manager.users.show', $user->id) }}" class="btn btn-outline-blue"><i
                class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card card-accent-primary mt-3">
                <div class="card-body justify-content-center w-100">
                    <p class="h4 text-center mb-3">{{ __('Добавление нового мультилинка') }}</p>
                    <form method="post">
                        @csrf
                        @if (session('success'))
                            <p class="text-success">
                                {{ session('success') }}
                            </p>
                        @endif
                        @if ($errors->any())
                            <div class="text-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-4">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">Пользователь</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control-plaintext font-weight-bold" id="staticEmail" value="{{ $user->email }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="link" class="col-sm-3 col-form-label">Ссылка</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="link" name="link">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Наименование</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-9 offset-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="setTemplate">
                                    <label class="form-check-label" for="setTemplate">Добавть шаблон</label>
                                </div>
                            </div>
                        </div>
                        <div id="templates"></div>
                        <div class="text-right">
                            <button class="btn btn-outline-blue" type="submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    var test = 0;
    var str = '';
    $('#setTemplate').on('click', function() {
        if(test === 0) {
            test = 1;

            $.ajax({
                type: "GET",
                url:"/get-templates",
                dataType: 'json',
                async: false,
                success: function (request) {

                    str += '<strong>Выберите шаблон:</strong>';
                    $.each(request.templates.data, function (key, value) {
                        str += '<div class="form-check">';
                        str += '    <input class="form-check-input" type="radio" name="template_id" id="templateRadios' + key + '" value="' + value.BotId + '">';
                        str += '    <label class="form-check-label" for="templateRadios' + key + '">' + 'Шаблон: ' + value.BotName + '</label>';
                        str += '</div>';
                    });
                    $('#templates').append(str);
                    str = '';

                }
            });

        } else {
            test = 0;
            $('#templates').empty();
        }

    })
</script>
@endsection
