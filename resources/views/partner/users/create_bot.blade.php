@extends('layouts.app')

@section('title', __('Создание авточата'))

@section('styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12 card card-accent-primary mt-3">
            <div class="card-body">
                <form method="post">
                    @csrf
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">{{ __('Ссылка на авточат') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" id="name" name="slug" placeholder="Имя пользователя" value="{{ old('slug') }}" required>
                            @if ($errors->has('slug'))
                            <span class="help-block text-danger">
                                <strong>{{ __('Проверьте правильность ввода') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">{{ __('Email пользователя') }}</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" placeholder="Email пользователя" value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                                <span class="help-block text-danger">
                                <strong>{{ __('Проверьте правильность ввода') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('partner::users::index') }}" class="btn btn-outline-danger">Отмена</a>
                        <button class="btn btn-outline-blue" type="submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
