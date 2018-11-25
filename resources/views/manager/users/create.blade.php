@extends('layouts.app')
@section('styles')
<link href="{{ asset('vendors/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('title', __('Добавление нового пользователя'))
@section('content')
    <div>
        <a href="{{ route('manager.users.index') }}" class="btn btn-outline-blue"><i
                    class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card card-accent-primary mt-3">
                <div class="card-body justify-content-center w-100">
                    <p class="h4 text-center mb-3">{{ __('Добавление нового пользователя') }}</p>
                    <form action="{{ route('manager.users.store') }}" method="post">
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
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">{{ __('Имя') }}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Имя пользователя" value="{{ __('Неизвестный') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email адрес" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select2-1" class="col-sm-2 col-form-label">{{ __('Код') }}</label>
                            <div class="col-sm-10">
                                {{--<select class="form-control">--}}
                                    {{--@foreach($phones as $phone)--}}
                                    {{--<option value="{{ $phone['code'] }}">{{ $phone['dial_code'] }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                                <select id="select2-1" class="form-control select2-single" name="code">
                                    @foreach($phones as $phone)
                                        <option value="{{ $phone['code'] }}" {{ $phone['code'] == 'KZ' ? 'selected' : '' }}>{{ $phone['name'] . ' ' . $phone['dial_code'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-sm-2 col-form-label">{{ __('Телефон') }}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Пример: 7777777777" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div>
                            <p class="text-danger">* Пароль генерируется автоматически</p>
                        </div>
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
    <script src="{{ asset('vendors/js/select2.min.js') }}"></script>
    <script>
        $('#select2-1').select2({
            theme: "bootstrap"
        });
    </script>
@endsection
